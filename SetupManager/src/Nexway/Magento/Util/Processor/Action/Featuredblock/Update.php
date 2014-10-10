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
class Update extends AbstractAction
{
    /**
     * @see AbstractAction::execute()
     */
    protected function _update()
    {
        $this->getParameters()->setModel('featured/blocks');

        $data = $this->getParameters()->getData('data');

        $data = $this->_implodeKeys($data, 'products');
        $data = $this->_implodeKeys($data, 'categories');

        $this->getParameters()->setData('data', $data);
        return parent::_update();
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
}
