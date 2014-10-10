<?php

namespace Nexway\Magento\Util\Processor\Action\Currencyrate;

use Nexway\Magento\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Mariusz Tasak <mtasak@nexway.com>
 * @copyright   Copyright (c) 2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     */
    protected function _create()
    {
        $data = $this->getParameters()->getData(self::F_DATA);
        \Mage::getModel('directory/currency')->saveRates($data);

        return true;
    }
}
