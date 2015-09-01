<?php

namespace Nexway\SetupManager\Util\Processor\Action\Cms;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 */
class Deleteall extends AbstractAction
{
    protected function _deleteall()
    {
        $this->getParameters()->setModel('cms/page');
        return parent::_deleteall();
    }
}
