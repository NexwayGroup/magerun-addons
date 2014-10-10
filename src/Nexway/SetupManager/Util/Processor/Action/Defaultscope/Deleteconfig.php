<?php

namespace Nexway\SetupManager\Util\Processor\Action\Defaultscope;

use Nexway\SetupManager\Util\Processor\AbstractConfigurationAction;

/**
 * Use action default/createconfig
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013, Nexway
 */
class Deleteconfig extends AbstractConfigurationAction
{
    public function execute()
    {
        $this->setScope('default');
        $this->_prepare();
        
        return $this->_deleteConfig();
    }
}
