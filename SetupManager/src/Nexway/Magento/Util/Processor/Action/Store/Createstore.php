<?php

namespace Nexway\Magento\Util\Processor\Action\Store;

use Nexway\Magento\Util\Processor\AbstractConfigurationAction;

/**
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013, Nexway
 */
class Createstore extends AbstractConfigurationAction
{
    public function execute()
    {
        return $this->_createStore();
    }
}
