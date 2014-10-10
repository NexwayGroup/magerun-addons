<?php

namespace Nexway\Magento\Util\Processor\Action\Featuredblock;

use Nexway\Magento\Util\Processor\AbstractAction;

/**
 * allow to create featured block
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @see Nexway_SetupManager_Model_Processor_AbstractAction::execute()
     */
    protected function _create()
    {
        $this->getParameters()->setModel('featured/blocks');
        $data = $this->getParameters()->getData('data');

        if ($this->_checkFeaturedBlockExist($data)) {
            throw new \Exception(
                "Featured block already exists '%s'", $data['block_id']
            );
        }

        $data = $this->_implodeKeys($data, 'products');
        $data = $this->_implodeKeys($data, 'categories');

        $this->getParameters()->setData('data', $data);
        return parent::_create();
    }

    /**
     * convert array of ids to coma separated string for given data type
     * 
     * @param array $data
     * @param string $dataType
     * @return array
     */
    protected function _implodeKeys(array $data, $dataType)
    {
        if (isset($data['automation_data'][$dataType])) {
            $ids = implode(',', $data['automation_data'][$dataType]);
            $data['automation_data'][$dataType] = $ids;
        }

        return $data;
    }

    protected function _checkFeaturedBlockExist($data)
    {
        /** @var \Nexway_Featured_Model_Blocks $blocks */
        $blocks = \Mage::getModel('featured/blocks')->getCollection();
        $blocks->setStoreFilter($data['store'], true)
            ->setBlockIdFilter($data['block_id']);

        if ($blocks->getSize() > 0) {
            return true;
        }

        return false;
    }
}
