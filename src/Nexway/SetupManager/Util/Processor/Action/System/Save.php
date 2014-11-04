<?php

namespace Nexway\SetupManager\Util\Processor\Action\System;

use Nexway\SetupManager\Util\Processor\AbstractSaveAction;
use Nexway\SetupManager\Util\Helper\Data;
use Nexway\SetupManager\Util\Helper\Parser;

/**
 * save whole system configuration
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @author       Marcin Rogacki <mrogacki@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Save extends AbstractSaveAction
{
    /**
     * @var array
     */
    private $_extendedIdValueHandlers = [
        'web/default/cms_home_page' => '_cmsExtendedId',
        'web/default/cms_no_route' => '_cmsExtendedId',
        'web/default/cms_no_cookies' => '_cmsExtendedId',
    ];

    /**
     * save whole system configuration
     */
    protected function _save()
    {
        $defaultConfig = $this->_getConfiguration('default');
        $websiteConfig = $this->_getConfiguration('website');
        $storeConfig   = $this->_getConfiguration('store');

        $this->_completeConfiguration = array_merge(
            $defaultConfig,
            $websiteConfig,
            $storeConfig,
            $this->_completeConfiguration
        );

        return TRUE;
    }

    /**
     * rad whole configuration from database for given scope type
     *
     * @param string $type
     * @return array
     */
    protected function _getConfiguration($type)
    {
        $config = [];
        if ($type !== 'default') {
            $scopeName = $type . 's';
        } else {
            $scopeName = $type;
        }

        try{
            $this->_showActionMessage(
                ['read configuration for ' . $type],
                $this->_configurationCounter
            );

            /** @var Parser $parser */
            $parser     = Parser::getInstance();
            $resource   = \Mage::getSingleton('core/resource');
            $tableName  = $resource->getTableName('core_config_data');
            $connection = $resource->getConnection('core_read');
            //$sql        = "SELECT * FROM $tableName WHERE scope = '$scopeName'";
            $config     = [];
            $sql        = $connection->select()
                ->from($tableName)
                ->where("scope='$scopeName'");

            foreach ($connection->fetchAll($sql) as $row) {
                $tempConfig = [
                    $type . '/createconfig',
                    'parameters'    => [
                        'key'       => $row['path'],
                        'value'     => $this->_createExtendedId($row['path'], $row['value']),
                    ]
                ];

                if ($type !== 'default') {
                    $code = \Mage::getModel('core/' . $type)->load($row['scope_id'])->getCode();
                    $tempConfig['parameters']['store'] = $parser->createExtendedId(
                        $type,
                        'code',
                        'id',
                        $code
                    );
                }

                $config[] = $tempConfig;
                $this->_configurationCounter++;
            }

            echo $this->_colorizeString("[OK]\n", 'green');
        }catch (\Exception $error) {
            echo $this->_colorizeString("[ERROR]\n", 'red');
            $this->_prepareErrorMessage($error,  $this->_configurationCounter);
        }

        return $config;
    }

    /**
     * @param string $path
     * @param string $value
     * @return string
     */
    private function _createExtendedId($path, $value)
    {
        if (isset($this->_extendedIdValueHandlers[$path])) {
            return call_user_func(
                [$this, $this->_extendedIdValueHandlers[$path]],
                $value
            );
        }
        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    private function _cmsExtendedId($id)
    {
        $cms = Data::getInstance()->getCmsModel();
        $cms->load($id);
        if ($cms->getId()) {

            $store = \Mage::getModel('core/store')->load($cms->getStoreId());
            if (NULL === $storeId = $store->getId()) {
                throw new \Exception(
                    'Unable to create extended id for cms page.'
                    . " Page with id '$id' has invalid store id: '$storeId'"
                );
            }

            $parser = Parser::getInstance();
            $storeExtId = $parser->createExtendedId(
                'store','code','id', $store->getCode()
            );
            return sprintf(
                'cms:identifier/%s,store_id/(%s)#page_id',
                rawurlencode($cms->getIdentifier()),
                $storeExtId
            );
        }

        throw new \Exception(
            'Unable to create extended id for cms page.'
            . " Page with id '$id' not found"
        );
    }
}
