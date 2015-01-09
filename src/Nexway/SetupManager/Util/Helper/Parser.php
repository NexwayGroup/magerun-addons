<?php
namespace Nexway\SetupManager\Util\Helper;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Łukasz Lach <llach@nexway.com>
 * @author      Michał Adamiak <madamiak@nexway.com>
 * @author      Marcin Rogacki <mrogacki@nexway.com>
 */
class Parser
{
    /**
     * @var string All extended ids surrended between () are treat as
     *      nasted extended ids
     */
    const GROUPED_EXTID_RX = "%\((.*)\)%";

    /**
     * Regular expression to parse exteneded id
     *   scope:field/value#extract
     */
    const EXTID_RX =
        '%
            ^
                (?P<scope>[a-z_]+)\:       # scope
                (?P<data>(.*))             # data
                \#(?:(?P<extract>.+))?     # extract
            $
         %x';

    /**
     * Regular expression to parse internal tags aka "{{...}}"
     */
    const INTERNAL_TAG_RX = '/\{\{[^\}]+\}\}/';

    /**
     * Common "id" field name
     */
    const F_ID = 'id';

    /**
     * Bitmask values used when converting internal ids
     * to extended ids both ways
     */
    const CONVERT_TO_EXTID    = 1;
    const CONVERT_FROM_EXTID  = 2;

    /**
     * Contains instance of processor currently running
     *
     * @var Processor
     */
    protected $_processor;

    /**
     * Allowed field names for scope - first field name in
     * every scope is considered default
     *
     * @var array
     */
    protected $_extIdAllowed = [
        'store'           => ['id', 'code', 'website_id'],
        'website'         => ['id', 'code'],
        'group'           => ['id'],
        'category'        => ['id', 'cocoon_id'],
        'product'         => ['id', 'sku', 'ref'],
        'featured_block'  => ['id', 'block_id'],
        'tax_class'       => ['id', 'class_name'],
        'tax_rate'        => ['id', 'code'],
        'tax_rule'        => ['id', 'code'],
        'attribute_set'   => ['id', 'name'],
        'agreements'      => ['id', 'name'],
        'customer_group'  => ['id', 'customer_group_code'],
        'image'           => ['id', 'local'],
    ];

    /**
     * @var array Allows to determine scopes for object which will be loaded
     *      by more than one field aka multicolumn unique key loader.
     */
    protected $_compundExtIdAllowed = [
        'cms'   => [ ['identifier', 'store_id'] ],
        'group' => [ ['name', 'website_id'] ],
    ];

    /**
     * @var array
     */
    protected $_extIdHandler = [
        'store'           => '_getStoreInstance',
        'website'         => '_getWebsiteInstance',
        'group'           => '_getGroupInstance',
        'category'        => '_getCategoryInstance',
        'product'         => '_getProductInstance',
        'featured_block'  => '_getFeaturedBlockInstance',
        'tax_class'       => '_getTaxClassInstance',
        'tax_rule'        => '_getTaxRuleInstance',
        'tax_rate'        => '_getTaxRateInstance',
        'attribute_set'   => '_getAttributeSetInstance',
        'agreements'      => '_getAgreementsInstance',
        'cms'             => '_getCmsInstance',
        'customer_group'  => '_getCustomerGroupInstance',
        'image'           => '_getImageInstance',
    ];

    /**
     * list of some problems with string parsing
     * @var string|boolean
     */
    public $warning = false;

    protected $_defaults;

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Parser();
        }

        return $instance;
    }

    private function __construct() {}

    /**
     * @return array
     */
    public function getAllowedExtendedIds()
    {
        return $this->_extIdAllowed;
    }

    /**
     * @return array
     */
    public function getAllowedCompoundExtendedIds()
    {
        return $this->_compundExtIdAllowed;
    }

    /**
     * Gets processor instance
     *
     * @return \Nexway\SetupManager\Util\Helper\Processor
     */
    public function getProcessor()
    {
        return $this->_processor;
    }

    /**
     * Sets processor instance
     *
     * @param \Nexway\SetupManager\Util\Helper\Processor $processor
     */
    public function setProcessor(Processor $processor)
    {
        $this->_processor = $processor;
    }

    /**
     * Parse config stored in PHP array and ready it for processor
     * or parse single string value containing extended id
     *
     * @param string|array $value
     * @return mixed
     */
    public function parseExtendedId($value)
    {
        $result         = null;
        $this->warning  = false;

        if (null === $this->_defaults) {
            $this->_defaults = [];
            $extIdAllowed = array_merge(
                $this->_extIdAllowed,
                $this->_compundExtIdAllowed
            );
            foreach ($extIdAllowed as $scope => $allowed) {
                $this->_defaults[$scope] = current($allowed);
            }
        }

        if (is_array($value)) {
            $result = $this->_parseExtendedIdArray($value);
        } elseif (is_string($value)) {
            // $value is passed by reference
            $this->_parseExtendedIdString($value);
            $result = $value;
        }

        return $result;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function _parseExtendedIdArray(array $config)
    {
        array_walk_recursive(
            $config,
            function (&$raw) {
                if (is_string($raw)) {
                    $this->_parseExtendedIdString($raw);
                }
            }
        );
        return $config;
    }

    /**
     * @param string $extId
     * @return string
     */
    private function _getGroupedExtendedId($extId)
    {
        preg_match(self::GROUPED_EXTID_RX, $extId, $matches);
        return isset($matches[1]) ? $matches[1] : false;
    }

    /**
     * @param string $extendedId
     * @param string $value
     * @return string
     */
    private function _replaceGroupedExtendedId($extendedId, $value)
    {
        return preg_replace(self::GROUPED_EXTID_RX, $value, $extendedId, -1);
    }

    /**
     * @param string $data
     * @return array
     */
    private function _formatExtendedIdData($data)
    {
        $formattedData = [];
        if (strpos($data, ',')) {
            $formattedData['fields'] = [];
            $formattedData['values'] = [];
            foreach (explode(',', $data) as $dataPair) {
                $explodedData = explode('/', $dataPair);
                $formattedData['fields'][] = (string)$explodedData[0];
                $formattedData['values'][] = rawurldecode((string)$explodedData[1]);
            }
        } else {
            $explodedData = explode('/', $data);
            $formattedData['fields'] = (string)$explodedData[0];
            $formattedData['values'] = rawurldecode((string)$explodedData[1]);
        }
        return $formattedData;
    }

    /**
     * @param string $scope
     * @return bool
     */
    private function _isAllowedExtendedId($scope)
    {
        return isset($this->_extIdAllowed[$scope])
        || isset($this->_compundExtIdAllowed[$scope]);
    }

    /**
     * @param string $scope
     * @param mixed $field String = plain ext id, array = compound ext id
     * @return bool
     */
    private function _isAllowedExtendedIdField($scope, $field)
    {
        if (is_array($field) && isset($this->_compundExtIdAllowed[$scope])) {
            foreach ($this->_compundExtIdAllowed[$scope] as $compoundFields) {
                if (!array_diff($field, $compoundFields)) {
                    return true;
                }
            }
        } elseif (isset($this->_extIdAllowed[$scope])
                   && in_array($field, $this->_extIdAllowed[$scope])
        ) {
            return true;
        }
        return false;
    }

    private function _isExtendedId($extendedIdToCheck)
    {
        return preg_match(self::EXTID_RX, $extendedIdToCheck, $matches);
    }

    /**
     * @param string $raw
     * @throws \Exception
     * @return bool
     */
    protected function _parseExtendedIdString(&$raw)
    {
        if (!$this->_isExtendedId($raw)) {
            return true;
        }
        if ($groupedExtendedId = $this->_getGroupedExtendedId($raw)) {
            if (!$this->_parseExtendedIdString($groupedExtendedId)) {
                throw new \Exception(
                    sprintf('Cannot parse grouped extended id "%s" of "%s"', $groupedExtendedId, $raw)
                );
            }

            $raw = $this->_replaceGroupedExtendedId($raw, $groupedExtendedId);
        }

        preg_match(self::EXTID_RX, $raw, $matches);
        $scope = $data = $extract = null;
        // extract only literal keys
        extract($matches, EXTR_OVERWRITE);
        $data = $this->_formatExtendedIdData($data);
        $fields = $data['fields'];
        $values = $data['values'];

        if ($this->_isAllowedExtendedId($scope)) {
            if (empty($fields)) {
                $fields = $this->_defaults[$scope];
            } elseif (!$this->_isAllowedExtendedIdField($scope, $fields)) {
                $fieldsForMsg = is_array($fields) ? implode(',', $fields) : $fields;
                throw new \Exception(
                    sprintf("Fields '%s' not allowed in scope '%s'", $fieldsForMsg, $scope)
                );
            }

            $result = call_user_func(
                [$this, $this->_extIdHandler[$scope]],
                $fields,
                $values
            );

            if (!is_object($result) || null === $result->getId()) {
                throw new \Exception(
                    sprintf('Unable to load extended id "%s"', $raw)
                );
            }

            if (isset($extract)) {
                if (self::F_ID == $extract) {
                    $result = $result->getId();
                } else {
                    if (!$result->hasData($extract)) {
                        throw new \Exception(
                            sprintf('No such field "%s" for %s', $extract, $raw)
                        );
                    }
                    $result = $result->getData($extract);
                }
            }
            $raw = $result;
        } else {
            $this->warning = "Invalid value '$raw'";
            return false;
        }
        return true;
    }

    /**
     * @param string $string
     * @param int $options
     * @return string
     */
    public function convert($string, $options = self::CONVERT_TO_EXTID)
    {
        $string = preg_replace_callback(
            self::INTERNAL_TAG_RX,
            function ($matches) use ($options) {
                $raw = explode(' ', substr($matches[0], 2, -2));
                $scope = array_shift($raw);
                $result = "{{{$scope} ";
                foreach ($raw as $param) {
                    list($param, $value) = explode('=', $param, 2);
                    if ($value[0] === '"' || $value[0] === '\'') {
                        $value = substr($value, 1, -1);
                    }

                    $fullParam = "$scope/$param";
                    switch ($fullParam) {
                        /** @todo when required to add new converting strategy,
                        change this to be genericly handled from array
                        of allowed params */
                        case 'widget/block_id':
                            // internal id => extended id
                            if ($options & self::CONVERT_TO_EXTID) {
                                $block = $this->_getFeaturedBlockInstance('id', $value);
                                if (!is_object($block)) {
                                    throw new \Exception(
                                        sprintf('Unable to translate featured block (id:%s)', $value)
                                    );
                                }
                                $blockId = $block->getBlockId();
                                $value = $this->createExtendedId(
                                    'featured_block',
                                    'block_id',
                                    'id',
                                    $blockId
                                );
                            } else {
                                // extended id => internal id
                                $this->_parseExtendedIdString($value);
                            }
                    }
                    $result .= "$param=\"$value\" ";
                }
                return rtrim($result)."}}";
            },
            $string
        );

        return $string;
    }

    /**
     * create extended id string from given values
     *
     * @param string $scope
     * @param string $type
     * @param string $name
     * @param string|integer $value
     * @return string
     */
    public function createExtendedId($scope, $type, $name, $value)
    {
        $value = rawurlencode($value);
        return "$scope:$type/$value#$name";
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception
     * @return \Mage_Core_Model_Store
     */
    protected function _getStoreInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getStoreModel()->load($value);
            case 'code':
                return $this->_getHelper()->getStoreModel()->load($value, 'code');
            case 'website_id':
                return $this->_getHelper()->getStoreModel()->load($value, 'website_id');
            default:
                throw new \Exception(sprintf('Invalid store field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception
     * @return \Mage_Catalog_Model_Category
     */
    protected function _getCategoryInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getCategoryModel()->load($value);
            case 'cocoon_id':
                return $this->_getHelper()->getCategoryModel()->loadByAttribute('cocoon_id', $value);
            default:
                throw new \Exception(sprintf('Invalid category field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception
     * @return \Mage_Core_Model_Website
     */
    protected function _getWebsiteInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getWebsiteModel()->load($value);
            case 'code':
                return $this->_getHelper()->getWebsiteModel()->load($value, 'code');
            default:
                throw new \Exception(sprintf('Invalid website field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception
     * @return \Nexway_Catalog_Model_Product|\Mage_Catalog_Model_Product
     */
    protected function _getProductInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getNexwayProductHelper()->getProductFromId($value);
            case 'ref':
                return $this->_getHelper()->getNexwayProductHelper()->getProductByProductRef($value);
            case 'sku':
                return $this->_getHelper()->getProductHelper()->getProduct($value, null, 'sku');
            default:
                throw new \Exception(sprintf('Invalid product field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception
     * @return \Mage_Core_Model_Website
     */
    protected function _getGroupInstance($field, $value)
    {
        if (is_array($field)) {
            $model = $this->_getHelper()->getStoreGroupModel();
            return $this->_getObjectInstanceFromCollection($model, $field, $value);
        }
        
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getWebsiteModel()->load($value);
            default:
                throw new \Exception(sprintf('Invalid group field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception
     * @return \Nexway_Featured_Model_Blocks
     */
    protected function _getFeaturedBlockInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getFeaturedBlockModel()->load($value, 'id');
            case 'block_id':
                return $this->_getHelper()->getFeaturedBlockModel()->load($value, 'block_id');
            default:
                throw new \Exception(sprintf('Invalid featured block field name "%s"', $field));
        }
    }

    protected function _getTaxClassInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getTaxClassModel()->load($value, 'id');
            case 'class_name':
                return $this->_getHelper()->getTaxClassModel()->load($value, 'class_name');
            default:
                throw new \Exception(sprintf('Invalid tax class field name "%s"', $field));
        }
    }

    protected function _getTaxRuleInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getTaxRuleModel()->load($value, 'id');
            case 'code':
                return $this->_getHelper()->getTaxRuleModel()->load($value, 'code');
            default:
                throw new \Exception(sprintf('Invalid tax rule field name "%s"', $field));
        }
    }


    protected function _getTaxRateInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getTaxRateModel()->load($value, 'id');
            case 'code':
                return $this->_getHelper()->getTaxRateModel()->load($value, 'code');
            default:
                throw new \Exception(sprintf('Invalid tax rate field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @return \Mage_Core_Model_Abstract|null
     * @throws \Exception
     */
    protected function _getAttributeSetInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getAttributesModel()->load($value, 'attribute_set_id');
            case 'name':
                return $this->_getHelper()->getAttributesModel()->load($value, 'attribute_set_name');
            default:
                throw new \Exception(sprintf('Invalid attribute set field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @return \Mage_Core_Model_Abstract|null
     * @throws \Exception
     */
    protected function _getAgreementsInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getAgreementModel()->load($value);
            case 'name':
                return $this->_getHelper()->getAgreementModel()->load($value, 'name');
            default:
                throw new \Exception(sprintf('Invalid agreement field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @return \Mage_Core_Model_Abstract|null
     * @throws \Exception
     */
    protected function _getCustomerGroupInstance($field, $value)
    {
        switch ($field) {
            case 'id':
                return $this->_getHelper()->getCustomerGroupModel()->load($value);
            case 'customer_group_code':
                return $this->_getHelper()->getCustomerGroupModel()->load($value, 'customer_group_code');
            default:
                throw new \Exception(sprintf('Invalid customer group field name "%s"', $field));
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @return \JR_CleverCms_Model_Cms_Page
     */
    protected function _getCmsInstance($field, $value)
    {
        $model = $this->_getHelper()->getCmsModel();
        $item = $this->_getObjectInstanceFromCollection($model, $field, $value);
        return $item;
    }

    /**
     * Returns image instance
     *
     * @param  string $field
     * @param  string $value
     * @return \Nexway_SetupManager_Model_Image
     * @throws \Exception
     */
    protected function _getImageInstance($field, $value)
    {
        switch ($field) {
            case 'local':
                return $this->_getHelper()->getImageModel()->fromLocalPath(
                    $this->getProcessor()->getPath(),
                    $value
                );
            default:
                throw new \Exception(sprintf('Invalid image field name "%s"', $field));
        }
    }

    /**
     * @param \Mage_Core_Model_Abstract $model
     * @param array $columns
     * @param array $values
     * @return \Mage_Core_Model_Abstract
     */
    private function _getObjectInstanceFromCollection(
        \Mage_Core_Model_Abstract $model,
        $columns,
        $values
    ) {
        $collection = $model->getCollection();
        $amountOfColumns = count($columns);
        for ($i=0; $i<$amountOfColumns; $i++) {
            if (isset($columns[$i]) && isset($values[$i])) {
                $collection->addFieldToFilter($columns[$i], $values[$i]);
            } else {
                return null;
            }
        }

        $item = $collection->getFirstItem();
        return !$item && !$item->getId() ? null : $item;
    }

    /**
     * @return Data
     */
    protected function _getHelper()
    {
        return Data::getInstance();
    }
}
