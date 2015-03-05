<?php

namespace Nexway\SetupManager\Util\Processor\Action\Salesrule;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Marcin Rogacki <mrogacki@nexway.com>
 * @copyright   Copyright (c) 2015, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     */
    protected function _create()
    {
        $this->getParameters()->setModel('salesrule/rule');

        return parent::_create();
    }
}
