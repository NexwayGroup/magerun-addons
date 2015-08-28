<?php

namespace Nexway\SetupManager\Util\Processor\Action\Cms;

use Nexway\SetupManager\Util\Processor\AbstractAction;

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
        $this->getParameters()->setModel('cms/page');
        return parent::_deleteall();
    }
}
