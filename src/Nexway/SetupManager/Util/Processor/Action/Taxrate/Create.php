<?php

namespace Nexway\SetupManager\Util\Processor\Action\Taxrate;

use Nexway\SetupManager\Util\Processor\AbstractAction;
use Nexway\SetupManager\Util\Helper\Parser;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     */
    protected function _create()
    {
        $parser = Parser::getInstance();
        $this->getParameters()->setModel('tax/calculation_rate');

        $data = $this->getParameters()->getData('data');
        if (isset($data['title'])){
            $parsedTitles = [];
            foreach($data['title'] as $key => $value) {
                $parsedTitles[$parser->parseExtendedId($key)] = $value;
            }
            
            $data['title'] = $parsedTitles;

            $this->getParameters()->setData('data', $data);
        }

        return parent::_create();
    }
}
