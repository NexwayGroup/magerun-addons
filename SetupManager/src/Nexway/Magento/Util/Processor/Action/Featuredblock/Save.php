<?php

namespace Nexway\Magento\Util\Processor\Action\Featuredblock;

use Nexway\Magento\Util\Processor\AbstractSaveAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * Save all feature blocks
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Łukasz Lach <llach@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Save extends AbstractSaveAction
{
    /**
     * Save all featured blocks
     * @return bool
     */
    protected function _save()
    {
        try {
            
        }catch (\Exception $error) {
            echo $this->_colorizeString("[ERROR]\n", 'red');
            $this->_prepareErrorMessage($error,  $this->_configurationCounter);
        }

        $blockModel    = \Mage::getModel('featured/blocks');
        
        if (!$blockModel) {
            echo $this->_colorizeString("[ERROR]\n", 'red');
            $this->_prepareErrorMessage(
                new \Exception('Can not load model featured/blocks'),
                $this->_configurationCounter
            );

            return true;
        }

        $blocks        = $blockModel->getCollection();
        $config        = [];
        foreach ($blocks as $block) {
            $blockData = $block->getData();
            $blockData['store'] = $this->_convertStoreIds($blockData['store']);
            unset($blockData['id']);
            $config[] = [
                'featuredblock/create',
                'parameters' => [
                    'data' => $blockData
                ]
            ];
            $this->_configurationCounter++;
        }

        $this->_completeConfiguration = array_merge(
            $this->_completeConfiguration,
            $config
        );

        return true;
    }

    /**
     * Convert store ids to exteneded id array
     * @param $storeIds
     * @return array
     */
    protected function _convertStoreIds($storeIds)
    {
        $parser = Parser::getInstance();
        $result = [];
        foreach ($storeIds as $storeId) {
            $store = \Mage::getModel('core/store')->load($storeId);
            $result[] = $parser->createExtendedId('store', 'code', 'id', $store->getCode());
        }

        return $result;
    }
}
