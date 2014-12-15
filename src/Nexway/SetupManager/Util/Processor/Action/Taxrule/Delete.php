<?php

namespace Nexway\SetupManager\Util\Processor\Action\Taxrule;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Delete extends AbstractAction
{
    const MODEL_ALIAS = 'tax/calculation_rule';

    /**
     * @return bool
     */
    protected function _delete()
    {
        $this->getParameters()->setModel(self::MODEL_ALIAS);
        $data           = $this->getParameters()->getData(self::F_DATA);


        /** @var \Mage_Tax_Model_Calculation_Rule $agreementModel */
        $agreementModel = \Mage::getModel(self::MODEL_ALIAS)->load(
            $data['target']
        );

        $agreementModel->delete();

        return true;
    }
}
