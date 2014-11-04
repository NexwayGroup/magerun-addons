<?php
namespace Nexway\SetupManager\Util\Processor\Action\Agreement;

use Nexway\SetupManager\Util\Processor\AbstractAction;
use Nexway\SetupManager\Util\Helper\Parser;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Update extends AbstractAction
{
    protected function _update()
    {
        $this->getParameters()->setModel('checkout/agreement');

        /** @var Parser $configParser */
        $configParser   = Parser::getInstance();
        $data           = $this->getParameters()->getData(self::F_DATA);

        $data['content'] = $configParser->convert(
            $data['content'],
            Parser::CONVERT_FROM_EXTID
        );

        /** @var \Mage_Cms_Model_Block $agreementModel */
        $agreementModel = \Mage::getModel('checkout/agreement')->load(
            $data['agreement_id']
        );
        $data = array_merge($agreementModel->getData(), $data);

        $agreementModel->setData($data);
        $agreementModel->save();

        return true;
    }
}
