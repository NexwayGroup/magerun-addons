<?php

namespace Nexway\SetupManager\Util\Processor\Action\Customergroup;

use Nexway\SetupManager\Util\Processor\AbstractAction;

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
