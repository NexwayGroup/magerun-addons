<?php

namespace Nexway\SetupManager\Util\Processor\Action\Agreement;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Create extends AbstractAction
{
    protected function _create()
    {   
        $this->getParameters()->setModel('checkout/agreement');
        return parent::_create();
    }
}
