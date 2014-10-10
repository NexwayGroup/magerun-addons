<?php

namespace Nexway\Magento\Util\Processor\Action\Block;

use Nexway\Magento\Util\Processor\AbstractAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * allow to create static block
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * override generic _create method to handle correct parent cms page
     *
     * @return bool
     */
    protected function _create()
    {
        $this->getParameters()->setModel('cms/block');
        /** @var Parser $configParser */
        $configParser   = Parser::getInstance();
        $data           = $this->getParameters()->getData(self::F_DATA);

        $data['content'] = $configParser->convert(
            $data['content'],
            Parser::CONVERT_FROM_EXTID
        );
        $this->getParameters()->setData(self::F_DATA, $data);

        return parent::_create();
    }
}
