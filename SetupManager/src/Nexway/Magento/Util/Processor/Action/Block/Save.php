<?php

namespace Nexway\Magento\Util\Processor\Action\Block;

use Nexway\Magento\Util\Processor\AbstractSaveAction;
use Nexway\Magento\Util\Helper\Parser;


/**
 * allow to create static block
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
     * add to configuration array all existing in database static blocks
     * with their configuration
     *
     * @todo find better solution to get store ids for block (not using direct query)
     */
    protected function _save()
    {
        /** @var \Mage_Cms_Model_Resource_Block_Collection $staticBlockCollection */
        $staticBlockCollection  = \Mage::getModel('cms/block')->getCollection();
        $this->_parser          = Parser::getInstance();

        /** @var \Nexway_Cms_Model_Block $staticBlock */
        foreach ($staticBlockCollection as $staticBlock) {
            try {
                $data           = $staticBlock->getData();
                $messageAction  = 'block/save/'
                    . $this->_truncate(
                        $data['title'], 40, '...'
                    );
                $this->_showActionMessage(
                    [$messageAction],
                    $this->_configurationCounter
                );

                $data['content']    = $this->_parser->convert($data['content']);
                $data['stores']     = $this->_joinBlockStoreIds($data['block_id']);
                unset($data['block_id']);

                $this->_completeConfiguration[] = [
                    'action'        => 'block/create',
                    'parameters'    => [
                        'data'      => $data,
                    ]
                ];

                echo $this->_colorizeString("[OK]\n", 'green');

            } catch (\Exception $error) {
                echo $this->_colorizeString("[ERROR]\n", 'red');
                $this->_prepareErrorMessage($error,  $this->_configurationCounter);
            }

            $this->_configurationCounter++;
        }

        return TRUE;
    }

    /**
     * get store id for static block and join them into array
     *
     * @param integer $blockId
     * @return array
     */
    protected function _joinBlockStoreIds($blockId)
    {
        /** @var \Mage_Core_Model_Resource $resource */
        $resource       = \Mage::getSingleton('core/resource');
        $list           = [];
        $readConnection = $resource->getConnection('core_read');
        $query          = $readConnection->select()
            ->from('cms_block_store')
            ->where("block_id='$blockId'");
        $results = $readConnection->fetchAll($query);

        foreach ($results as $store) {
            $code       = \Mage::getModel('core/store')->load($store['store_id'])->getCode();
            $extendedId = $this->_parser->createExtendedId('store', 'code', 'id', $code);
            $list[]     = $extendedId;
        }

        return $list;
    }
}
