<?php

namespace Nexway\SetupManager\Util\Processor\Action\Agreement;

use Nexway\SetupManager\Util\Processor\AbstractSaveAction;
use Nexway\SetupManager\Util\Helper\Parser;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2013-2014, Nexway
 */
class Save extends AbstractSaveAction
{
    protected function _save()
    {
        /** @var Parser $parser */
        $parser         = Parser::getInstance();
        $agreementList  = \Mage::getModel('checkout/agreement')->getCollection();
        $configuration  = array();
        $action         = 'agreement/create';

        foreach ($agreementList as $agreement) {
            $agreement->load($agreement->getId());
            $data          = $agreement->getData();
            $storeIds      = $data['store_id'];
            $messageAction = 'agreement/save/' . $this->_truncate($data['name'], 40, '...');

            $this->_showActionMessage(
                [$messageAction], $this->_configurationCounter
            );

            foreach ($storeIds as &$storeId){
                $code    = \Mage::getModel('core/store')->load($storeId)->getCode();
                $storeId = $parser->createExtendedId('store', 'code', 'id', $code);
            }

            $data['stores'] = $storeIds;
            unset($data['agreement_id']);
            unset($data['store_id']);

            $configuration[] = [
                'action'        => $action,
                'parameters'    => [
                    'data'      => $data,
                ]
            ];

            echo $this->_colorizeString("[OK]\n", 'green');
            $this->_configurationCounter++;
        }

        $this->_completeConfiguration = array_merge(
            $configuration,
            $this->_completeConfiguration
        );
    }
}
