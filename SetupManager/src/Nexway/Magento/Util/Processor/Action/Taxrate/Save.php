<?php

namespace Nexway\Magento\Util\Processor\Action\Taxrate;

use Nexway\Magento\Util\Processor\AbstractSaveAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Save extends AbstractSaveAction
{
    /**
     * @return bool
     */
    public function _save()
    {
        $this->_completeConfiguration = array_merge(
            $this->getTaxRateConfiguration(),
            $this->_completeConfiguration
        );
        
        return true;
    }

    /**
     * @return array
     */
    public function getTaxRateConfiguration()
    {
        $taxRates          = [];
        $parser            = Parser::getInstance();
        $taxRateCollection = \Mage::getModel('tax/calculation_rate')->getCollection();

        foreach($taxRateCollection as $taxRate) {
            try{
                $this->_showActionMessage(
                    ['save tax rate - ' . $taxRate->getCode()],
                    $this->_configurationCounter
                );

                $data          = $taxRate->getData();
                $data['title'] = [];
                $titles        = $taxRate->getTitles()->getData();

                foreach($titles as $title) {
                    $store = \Mage::getModel('core/store')->load($title['store_id']);
                    $extendedStoreId = $parser->createExtendedId('store', 'code', 'id', $store->getCode());
                    $data['title'][$extendedStoreId] = $title['value'];
                }

                unset($data['tax_calculation_rate_id']);

                $taxRates[] = [
                    'taxrate/create',
                    'parameters'    => [
                        'data' => $data
                    ]
                ];

                $this->_configurationCounter++;
                echo $this->_colorizeString("[OK]\n", 'green');
            }catch (\Exception $error) {
                echo $this->_colorizeString("[ERROR]\n", 'red');
                $this->_prepareErrorMessage($error,  $this->_configurationCounter);
            }
        }
        
        return $taxRates;
    }
}
