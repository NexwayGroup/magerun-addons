<?php

namespace Nexway\Magento\Util\Processor;

/**
 * allow to create, update or remove system configuration
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013, Nexway
 */
class AbstractConfigurationAction extends AbstractAction
{
    /**
     * @var \Mage_Core_Model_Config
     */
    protected $_configModel;

    /**
     * @var string
     */
    protected $_scope;

    public function getConfigModel()
    {
        if (null === $this->_configModel) {
            $this->_configModel = \Mage::getModel('core/config');
        }
        return $this->_configModel;
    }

    public function setScope($scope)
    {
        $this->_scope = $scope;
        switch ($scope) {
            case 'website':
            case 'store':
                $this->_scope .= 's';
                break;
            default:
                break;
        }
        return $this;
    }

    public function getScope()
    {
        if (null === $this->_scope) {
            throw new \Exception("Scope not set!");
        }
        return $this->_scope;
    }

    /**
     * override parent execute method to set configuration model
     *
     * @return bool
     */
    public function execute()
    {
        $this->_prepare();
        return parent::execute();
    }

    /**
     * return void
     */
    protected function _prepare()
    {
        $this->_configModel = \Mage::getModel('core/config');
        if (null === $this->getParameters()->getStore()) {
            $this->getParameters()->setStore(0);
        }
    }

    /**
     * set system configuration for given scope
     *
     * @return bool
     */
    protected function _createConfig()
    {
        $this->getConfigModel()->saveConfig(
            $this->getParameters()->getKey(),
            $this->getParameters()->getValue(),
            $this->getScope(),
            $this->getParameters()->getStore()
        );

        return true;
    }

    /**
     * remove system configuration for given scope
     * (or set on default if scope is stores or websites)
     *
     * @return bool
     */
    protected function _deleteConfig()
    {
        $this->getConfigModel()->deleteConfig(
            $this->getParameters()->getKey(),
            $this->getScope(),
            $this->getParameters()->getStore()
        );

        return true;
    }

    /**
     * create website
     *
     * @return bool
     */
    protected function _createWebsite()
    {
        \Mage::getModel('install/resource_setup')->createStoreWebsite(
            [
                'code'  => $this->getParameters()->getCode(),
                'name'  => $this->getParameters()->getName()
            ]
        );

        return true;
    }

    /**
     * create store view
     *
     * @return bool
     */
    protected function _createStore()
    {
        \Mage::getModel('install/resource_setup')->createStoreView(
            [
                'name'              => $this->getParameters()->getName(),
                'code'              => $this->getParameters()->getCode(),
                'is_active'         => 1,
                'group_id'          => $this->getParameters()->getGroup(),
                'website_id'        => $this->getParameters()->getWebsite()
            ]
        );

        return true;
    }

    /**
     * create group for store view
     *
     * @return bool
     */
    protected function _createGroup()
    {
        \Mage::getModel('install/resource_setup')->createStoreGroup(
            [
                'name'              => $this->getParameters()->getName(),
                'root_category_id'  => $this->getParameters()->getRootCategory(),
                'website_id'        => $this->getParameters()->getWebsite()
            ]
        );

        return true;
    }
}
