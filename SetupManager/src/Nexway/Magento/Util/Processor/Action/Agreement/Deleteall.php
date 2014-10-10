<?php

namespace Nexway\Magento\Util\Processor\Action\Agreement;

use Nexway\Magento\Util\Processor\AbstractAction;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Deleteall extends AbstractAction
{
    protected function _deleteall()
    {
        $this->getParameters()->setModel('checkout/agreement');
        return parent::_deleteall();
    }
}
