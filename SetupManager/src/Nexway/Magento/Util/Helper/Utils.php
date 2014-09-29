<?php
namespace Nexway\Magento\Util\Helper;
/**
 * @category   Nexway
 * @package    Nexway_SetupManager
 * @author     Grzegorz Pawlik <gpawlik@nexway.com>
 * @author     Christophe Eble <ceble@nexway.com>
 */
class Utils
{
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Utils();
        }

        return $instance;
    }

    private function __construct() {}

    /*
     * allows defining more readable path=>val config for stores
     * check tests for examples
     */
    const PARAMETERS_KEY = 'parameters';
    const STORE_KEY = 'store';

    /**
     * @const string Product update action
     */
    const PRODUCT_UPDATE_ACTION         = 'product/update';

    /**
     * @const string Config actions
     */
    const CREATE_DEFAULT_CONFIG_ACTION  = 'default/createconfig';
    const CREATE_WEBSITE_CONFIG_ACTION  = 'website/createconfig';
    const CREATE_STORE_CONFIG_ACTION    = 'store/createconfig';

    /**
     * extracts config from squeezed form
     *
     * from
     * <pre>
     * this->createConfigForStore('extended:id', [
     *    'key1' => 'value1',
     *    'key2' => 'value2',
     * ]);
     * </pre>
     * into:
     * [
     *   [
     *      'store/createconfig',
     *      [
     *          'key' => 'key1',
     *          'value' => 'value1',
     *          'store' => 'extended:id'
     *      ]
     *   ],
     *   [
     *      'store/createconfig',
     *      [
     *          'key' => 'key2',
     *          'value' => 'value2',
     *          'store' => 'extended:id'
     *      ]
     *   ],
     * ]
     * please check tests for more examples.
     *
     *
     * @param $storeExtendedId
     * @param $squeezedConfig
     * @return array
     */
    public function createConfigForStore($storeExtendedId, $squeezedConfig)
    {
        $fullConfig = [];
        foreach ($squeezedConfig as $key => $value) {
            $fullConfig[] = $this->createConfigEntry(
                self::CREATE_STORE_CONFIG_ACTION,
                $key,
                $value,
                [self::STORE_KEY => $storeExtendedId]
            );
        }

        return $fullConfig;
    }

    /**
     * extract
     *
     * @param $squeezedConfig
     * @return array
     */
    public function createConfigForDefault($squeezedConfig)
    {
        $fullConfig = [];
        foreach ($squeezedConfig as $key => $value) {
            $fullConfig[] = $this->createConfigEntry(
                self::CREATE_DEFAULT_CONFIG_ACTION,
                $key,
                $value
            );
        }

        return $fullConfig;
    }

    /**
     * updates/create product globally
     *
     * @example
     *
     * '''
     *   $utils->updateProductForDefault('backup-dvd', [
     *       'type_id'               => 'virtual',
     *       'attribute_set_id'      => 'attribute_set:name/Services#id',
     *      ...
     *   ])
     * '''
     *
     * @param  string $sku  SKU ex: backup-dvd
     * @param  array  $data Product data
     * @return array
     */
    public function updateProductForDefault($sku, $data)
    {
        $fullConfig[] = $this->createProductDataEntry(
            self::PRODUCT_UPDATE_ACTION, $sku, null, $data
        );

        return $fullConfig;
    }

    /**
     * updates product in given store(s)
     *
     * This task is able to produce multiple data entries based on $storeExtendedId type array|string
     * This is proven useful for managing common data entries for multiple storeviews.
     *
     * @example
     *
     * '''
     *   $utils->updateProductForStore([
     *       'store:code/at_de#id', 'store:code/ch_fr#id', 'store:code/ch_de#id',
     *       'store:code/ch_it#id'
     *   ], 'backup-dvd', [
     *       'type_id'               => 'virtual',
     *       'attribute_set_id'      => 'attribute_set:name/Services#id',
     *      ...
     *   ])
     * '''
     *
     * @param  string|array $storeExtendedId Extended ID ex: store:code/at_de#id
     * @param  string       $sku             SKU ex: backup-dvd
     * @param  array        $data            Product data
     * @return array
     */
    public function updateProductForStore($storeExtendedId, $sku, $data)
    {
        $fullConfig = [];
        if (!is_array($storeExtendedId)) {
            $storeExtendedId = [$storeExtendedId];
        }

        // Process multiple store ids
        foreach ($storeExtendedId as $processedStoreId) {
            $fullConfig[] = $this->createProductDataEntry(
                self::PRODUCT_UPDATE_ACTION, $sku, $processedStoreId, $data
            );
        }

        return $fullConfig;
    }

    /**
     * DEPRECATED! We just keep it so old config can work
     * @param string $storeExtractionPath
     * @param array $squeezedConfig
     * @return array
     * @deprecated please use createConfigForStore
     */
    public function configForStore($storeExtractionPath, $squeezedConfig)
    {
        return $this->createConfigForStore(
            $storeExtractionPath,
            $squeezedConfig
        );
    }

    /**
     * creates config entry from params and merges with defaults
     *
     * defaults can overwrite params, so be careful
     *
     * @param $action
     * @param $key
     * @param $value
     * @param array $defaults
     * @return array
     */
    protected function createConfigEntry($action, $key, $value, $defaults = [])
    {
        return [
            $action,
            self::PARAMETERS_KEY => array_merge(
                $defaults,
                [
                    'key'   => $key,
                    'value' => $value
                ]
            )
        ];
    }

    /**
     * creates data entry from params
     *
     * @param string $action          Action name
     * @param string $sku             SKU
     * @param string $storeExtendedId Store Extended ID
     * @param array  $data            Product data
     * @return array
     */
    protected function createProductDataEntry($action, $sku, $storeExtendedId = null, array $data)
    {
        // Explicitly set store id and sku to avoid mistakes!
        $data['sku'] = $sku;

        if (!is_null($storeExtendedId)) {
            $data['store_id'] = $storeExtendedId;
        }

        return [
            $action,
            self::PARAMETERS_KEY => ['data' => $data]
        ];
    }

    /**
     * extracts squeezed config
     * @see createConfigForStore for explanation
     *
     * @param string $websiteExtendedId
     * @param array $squeezedConfig
     * @return array
     */
    public function createConfigForWebsite(
        $websiteExtendedId,
        $squeezedConfig
    ) {
        $fullConfig = [];
        foreach ($squeezedConfig as $key => $value) {
            $fullConfig[] = $this->createConfigEntry(
                self::CREATE_WEBSITE_CONFIG_ACTION,
                $key,
                $value,
                [self::STORE_KEY => $websiteExtendedId]
            );
        }

        return $fullConfig;
    }

    /**
     * squeezes config values
     *
     * output is ready to be saved in php file
     *
     * @param array $configArray
     * @return string
     * @throws \UnexpectedValueException
     */
    public function squeeze($configArray)
    {
        $this->assertOnlyConfigEntries($configArray);

        $returnString = "<?php \n"
            . "  return array_merge(\n"
            . "      {$this->squeezeDefaults($configArray)},\n"
            . "      {$this->squeezeWebsites($configArray)},\n"
            . "      {$this->squeezeStores($configArray)}\n"
            . ");";

        return $returnString;
    }

    /**
     * @param $configArray
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function squeezeDefaults($configArray)
    {
        $returnString = "";
        foreach ($configArray as $item) {
            if ($item[0] === self::CREATE_DEFAULT_CONFIG_ACTION) {
                $returnString .= $this->entrySpacing() . "'{$item[self::PARAMETERS_KEY]['key']}' => " .
                    " '{$this->escapeValue($item[self::PARAMETERS_KEY]['value'])}',\n";
            }
        }

        return "\$utils->createConfigForDefault([\n" .
        $returnString .
        "\n])";
    }

    /**
     * @param $data
     * @param $actionKey
     * @param $utilsMethodName
     * @param string $key
     * @return string
     */
    protected function squeezeWebsiteOrStore($data, $actionKey, $utilsMethodName, $key = self::STORE_KEY)
    {
        $entries = [];

        // collect by website selector
        foreach ($data as $params) {
            if ($params[0] === $actionKey) {
                $entries[$params[self::PARAMETERS_KEY][self::STORE_KEY]][$params[self::PARAMETERS_KEY]['key']] =
                    $params[self::PARAMETERS_KEY]['value'];
            }
        }

        $squeezedEntries = [];
        foreach ($entries as $storeSelector => $websiteConfig) {
            $squeezedEntries[$storeSelector] = "\n\$utils->$utilsMethodName('$storeSelector', [\n";
            foreach ($websiteConfig as $key => $val) {
                $squeezedEntries[$storeSelector] .=
                    $this->entrySpacing() . "'$key' => '{$this->escapeValue($val)}',\n";
            }
            $squeezedEntries[$storeSelector] .= "])";
        }

        if (empty($squeezedEntries)) {
            return '[]';
        }

        return implode(",\n", $squeezedEntries);
    }

    /**
     * @param $configArray
     * @return string
     */
    protected function squeezeWebsites($configArray)
    {
        return $this->squeezeWebsiteOrStore(
            $configArray, self::CREATE_WEBSITE_CONFIG_ACTION, 'createConfigForWebsite'
        );
    }

    /**
     * @param $configArray
     * @return string
     */
    protected function squeezeStores($configArray)
    {
        return $this->squeezeWebsiteOrStore(
            $configArray, self::CREATE_STORE_CONFIG_ACTION, 'createConfigForStore'
        );
    }

    /**
     * escapes config values
     *
     * @param $str
     * @return mixed
     */
    protected function escapeValue($str)
    {
        return str_replace("'", "\\'", $str);
    }

    /**
     * place to keep all spaces together so we'll not lose and can use for nice squeezed configs formatting
     *
     * @return string
     */
    protected function entrySpacing()
    {
        return "           ";
    }

    /**
     * @param $configArray
     */
    private function assertOnlyConfigEntries($configArray)
    {
        foreach ($configArray as $item) {
            if (!in_array($item[0], [
                self::CREATE_DEFAULT_CONFIG_ACTION, self::CREATE_WEBSITE_CONFIG_ACTION,
                self::CREATE_STORE_CONFIG_ACTION,   self::PRODUCT_UPDATE_ACTION
            ])) {
                throw new \UnexpectedValueException(
                    "squeeze can only operate on config entries, '{$item[0]}' found"
                );
            }
        }
    }

    /**
     * generate random prefix for order number
     */
    public function generateRandomOrderPrefix()
    {
        $randomInt      = '';
        $randomChars    = '';

        for ($i = 0; $i < 3; $i++) {
            $randomInt      .= rand(0, 9);
            $randomChars    .= chr(rand(65, 90));
        }

        return $randomChars . $randomInt;
    }
}
