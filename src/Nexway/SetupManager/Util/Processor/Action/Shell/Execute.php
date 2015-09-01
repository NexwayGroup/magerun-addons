<?php

namespace Nexway\SetupManager\Util\Processor\Action\Shell;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Marcin Rogacki <mrogacki@nexway.com>
 * @copyright   Copyright (c) 2015, Nexway
 */
class Execute extends AbstractAction
{
    /**
     * @return bool
     */
    public function execute()
    {
        try {
            $command = $this->getParameters()->getCommand();
            $result = system(escapeshellcmd($command), $returnCode);
            return false !== $result && 0 === $returnCode;
        } catch (Exception $e) {
            return false;
        }
    }
}
