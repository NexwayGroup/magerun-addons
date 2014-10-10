<?php

namespace Nexway\Magento\Util\Processor\Action\Stores;

use Nexway\Magento\Util\Processor\AbstractSaveAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * save all websites and stores
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Save extends AbstractSaveAction
{
    /**
     * @var Parser
     */
    protected $_parser;

    /**
     * save all websites and stores
     */
    protected function _save()
    {
        $this->_parser  = Parser::getInstance();
        $websites       = $this->_saveWebsites();
        $groups         = $this->_saveGroups();
        $stores         = $this->_saveStores();

        $this->_completeConfiguration = array_merge(
            $websites,
            $groups,
            $stores,
            $this->_completeConfiguration
        );

        return true;
    }

    /**
     * save all websites
     *
     * @return array
     */
    protected function _saveWebsites()
    {
        $config = [];
        foreach (\Mage::app()->getWebsites() as $website) {
            $this->_showActionMessage(
                ['save website: ' . $website->getName()],
                $this->_configurationCounter
            );

            $config[] = [
                'website/createwebsite',
                'parameters'    => [
                    'code'   => $website->getCode(),
                    'name'   => $website->getName(),
                ]
            ];

            echo $this->_colorizeString("[OK]\n", 'green');
            $this->_configurationCounter++;
        }

        return $config;
    }

    /**
     * save all groups
     *
     * @return array
     */
    protected function _saveGroups()
    {
        $config = [];
        foreach (\Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                try{
                    $this->_showActionMessage(
                        ['save group: ' . $group->getName()],
                        $this->_configurationCounter
                    );
                    $code = \Mage::getModel('core/website')
                        ->load($group['website_id'])
                        ->getCode();

                    $config[] = [
                        'group/creategroup',
                        'parameters'    => [
                            'name'              => $group->getName(),
                            'root_category'     => 0,
                            'website'           => $this->_parser->createExtendedId('website', 'code', 'id', $code),
                        ]
                    ];

                    echo $this->_colorizeString("[OK]\n", 'green');
                }catch (\Exception $error) {
                    echo $this->_colorizeString("[ERROR]\n", 'red');
                    $this->_prepareErrorMessage($error,  $this->_configurationCounter);
                }

                $this->_configurationCounter++;
            }
        }

        return $config;
    }

    /**
     * save all stores
     *
     * @return array
     */
    protected function _saveStores()
    {
        $config = [];
        foreach (\Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {

                foreach ($group->getStores() as $store) {
                    try{
                        $this->_showActionMessage(
                            ['save store: ' . $store->getName()],
                            $this->_configurationCounter
                        );
                        $code = \Mage::getModel('core/website')
                            ->load($group['website_id'])
                            ->getCode();

                        $config[] = [
                            'store/createstore',
                            'parameters'    => [
                                'name'        => $store->getName(),
                                'code'        => $store->getCode(),
                                'group'       => $this->_parser->createExtendedId('group', 'id', 'default_group_id', $code),
                                'website'     => $this->_parser->createExtendedId('website', 'code', 'id', $code),
                            ]
                        ];

                        echo $this->_colorizeString("[OK]\n", 'green');
                    }catch (\Exception $error) {
                        echo $this->_colorizeString("[ERROR]\n", 'red');
                        $this->_prepareErrorMessage($error,  $this->_configurationCounter);
                    }

                    $this->_configurationCounter++;
                }
            }
        }

        return $config;
    }
}
