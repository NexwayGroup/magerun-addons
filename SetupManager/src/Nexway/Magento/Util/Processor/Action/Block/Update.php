<?php

namespace Nexway\Magento\Util\Processor\Action\Block;

use Nexway\Magento\Util\Processor\AbstractAction;
use Nexway\Magento\Util\Helper\Parser;


/**
 * allow to update static block
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Update extends AbstractAction
{
    /**
     * @see Nexway_SetupManager_Model_Processor_AbstractAction::execute()
     */
    protected function _update()
    {
        $this->getParameters()->setModel('cms/block');
        /** @var Parser $configParser */
        $configParser   = Parser::getInstance();
        $data           = $this->getParameters()->getData(self::F_DATA);

        $data['content'] = $configParser->convert(
            $data['content'],
            Parser::CONVERT_FROM_EXTID
        );

        /** @var \Mage_Cms_Model_Block $cmsModel */
        $staticBlockModel = \Mage::getModel('cms/block')->load(
            $data['identifier'],
            'identifier'
        );
        $data = array_merge($staticBlockModel->getData(), $data);

        $staticBlockModel->setData($data);
        $staticBlockModel->save();

        return true;
    }
}
