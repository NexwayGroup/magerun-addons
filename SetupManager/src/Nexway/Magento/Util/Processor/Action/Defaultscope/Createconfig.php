<?php

namespace Nexway\Magento\Util\Processor\Action\Defaultscope;

use Nexway\Magento\Util\Processor\AbstractConfigurationAction;

/**
 * Use action default/createconfig
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-24, Nexway
 */
class Createconfig extends AbstractConfigurationAction
{
    public function execute()
    {
        $this->setScope('default');
        $this->_prepare();

        return $this->_createConfig();
    }
}
