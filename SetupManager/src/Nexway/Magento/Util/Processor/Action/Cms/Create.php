<?php

namespace Nexway\Magento\Util\Processor\Action\Cms;

use Nexway\Magento\Util\Processor\AbstractAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * allow to create cms page
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * override generic _create method to handle correct parent cms page
     *
     * @return bool
     */
    protected function _create()
    {
        $this->getParameters()->setModel('cms/page');
        /** @var Parser $configParser */
        $configParser   = Parser::getInstance();
        $data           = $this->getParameters()->getData(self::F_DATA);

        if ($data['parent_id'] !== '0' || !isset($data['parent_id'])) {
            $id = $this->_findParentId($data['store_id']);
            $data['parent_id'] = $id;
        }

        $data['content'] = $configParser->convert(
            $data['content'],
            Parser::CONVERT_FROM_EXTID
        );
        $this->getParameters()->setData(self::F_DATA, $data);

        return parent::_create();
    }

    /**
     * get parent id by children store id
     * 
     * @param integer $storeId
     * @return null|integer
     */
    protected function _findParentId($storeId)
    {
        $collection = \Mage::getModel('cms/page')->getCollection()
            ->addStoreFilter($storeId);

        foreach ($collection as $c) {
            if ($c->getParentId() === '0' && $c->getStoreId() === $storeId) {
                return $c->getId();
            }
        }

        return null;
    }
}
