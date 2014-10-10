<?php

namespace Nexway\SetupManager\Util\Processor\Action\Group;

use Nexway\SetupManager\Util\Processor\AbstractConfigurationAction;

/**
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Creategroup extends AbstractConfigurationAction
{
    public function execute()
    {
        return $this->_createGroup();
    }
}
