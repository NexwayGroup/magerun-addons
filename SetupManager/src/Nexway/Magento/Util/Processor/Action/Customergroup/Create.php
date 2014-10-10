<?php

namespace Nexway\Magento\Util\Processor\Action\Customergroup;

use Nexway\Magento\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     */
    protected function _create()
    {
        $this->getParameters()->setModel('customer/group');

        return parent::_create();
    }
}
