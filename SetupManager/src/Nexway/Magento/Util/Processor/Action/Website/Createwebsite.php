<?php

namespace Nexway\Magento\Util\Processor\Action\Website;

use Nexway\Magento\Util\Processor\AbstractConfigurationAction;

/**
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013, Nexway
 */
class Createwebsite extends AbstractConfigurationAction
{
    public function execute()
    {
        return $this->_createWebsite();
    }
}
