<?php

namespace Nexway\SetupManager\Util\Processor\Action\Cms;

use Nexway\SetupManager\Util\Processor\AbstractAction;
use Nexway\SetupManager\Util\Helper\Parser;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013, Nexway
 */
class Update extends AbstractAction
{
    /**
     * @see Nexway_SetupManager_Model_Processor_AbstractAction::execute()
     */
    protected function _update()
    {
        $this->getParameters()->setModel('cms/page');
        /** @var Parser $configParser */
        $configParser   = Parser::getInstance();
        $data           = $this->getParameters()->getData(self::F_DATA);

        $data['content'] = $configParser->convert(
            $data['content'],
            Parser::CONVERT_FROM_EXTID
        );

        /** @var \Mage_Cms_Model_Page $cmsModel */
        $cmsModel   = \Mage::getModel('cms/page')->load($data['identifier']);
        $data       = array_merge($cmsModel->getData(), $data);

        $cmsModel->setData($data);
        $cmsModel->save();

        return true;
    }
}
