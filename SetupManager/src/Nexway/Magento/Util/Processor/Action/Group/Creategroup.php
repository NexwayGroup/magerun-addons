<?php

namespace Nexway\Magento\Util\Processor\Action\Group;

use Nexway\Magento\Util\Processor\AbstractConfigurationAction;

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
