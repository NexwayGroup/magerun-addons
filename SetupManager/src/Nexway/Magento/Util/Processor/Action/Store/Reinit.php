<?php

namespace Nexway\Magento\Util\Processor\Action\Store;

use Nexway\Magento\Util\Processor\AbstractAction;

/**
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Marcin Rogacki <mrogacki@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Reinit extends AbstractAction
{
    /**
     * 
     */
    public function execute()
    {
        \Mage::app()->reinitStores();
        return true;
    }
}
