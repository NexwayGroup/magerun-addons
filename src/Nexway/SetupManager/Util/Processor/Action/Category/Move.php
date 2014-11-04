<?php

namespace Nexway\SetupManager\Util\Processor\Action\Block;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Łukasz Lach <llach@nexway.com>
 *
 * Example action:
 *   [
 *     'category/move',
 *     [
 *       'target' => 'category:cocoon_id/Games_7594',
 *       'to'     => 'category:cocoon_id/Games#id'
 *     ]
 *   ]
 */
class Move extends AbstractAction
{
    public function execute()
    {
        /**
         * @var \Nexway_Catalog_Model_Category
         */
        $target = $this->getParameters()->getTarget();
        $to     = $this->getParameters()->getTo();
        $after  = $this->getParameters()->getAfter() ?: 0;
        if (is_object($to)) {
            $to = $to->getId();
        }
        $result = $target->move($to, $after);
        return true;
    }
}
