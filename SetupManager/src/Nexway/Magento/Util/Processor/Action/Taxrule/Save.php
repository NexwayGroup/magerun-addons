<?php

namespace Nexway\Magento\Util\Processor\Action\Taxrule;

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
    /** @var Parser */
    private $_parser = null;

    /**
     * @return bool
     */
    public function _save()
    {
        $this->_completeConfiguration = array_merge(
            $this->getTaxRuleConfiguration(),
            $this->_completeConfiguration
        );
        
        return true;
    }
    
    public function getTaxRuleConfiguration()
    {
        $taxRules = [];

        $taxRuleCollection = \Mage::getModel('tax/calculation_rule')->getCollection();

        foreach($taxRuleCollection as $taxRule) {
            try{
                $this->_showActionMessage(
                    ['save tax rule - ' . $taxRule->getCode()],
                    $this->_configurationCounter
                );

                $data                       = $taxRule->getData();
                $rates                      = array_unique($taxRule->getRates());
                $customerTaxClasses         = array_unique($taxRule->getCustomerTaxClasses());
                $productTaxClasses          = array_unique($taxRule->getProductTaxClasses());
                $data['tax_rate']           = $this->_convertRateIdsToExtendedIds($rates);
                $data['tax_customer_class'] = $this->_convertClassIdsToExtendedIds($customerTaxClasses);
                $data['tax_product_class']  = $this->_convertClassIdsToExtendedIds($productTaxClasses);

                unset($data['tax_calculation_rule_id']);

                $taxRules[] = [
                    'taxrule/create',
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
        
        return $taxRules;
    }

    /**
     * @return Parser
     */
    protected function _getParser()
    {
        return Parser::getInstance();
    }

    /**
     * @param array $rateIds
     * @return array
     */
    protected function _convertRateIdsToExtendedIds($rateIds)
    {
        $extendedRateIds = [];

        foreach($rateIds as $rateId) {
            $taxRate           = \Mage::getModel('tax/calculation_rate')->load($rateId);
            $extendedRateIds[] = $this->_getParser()->createExtendedId('tax_rate', 'code', 'id', $taxRate->getCode());
        }

        return $extendedRateIds;
    }

    /**
     * @param array $classIds
     * @return array
     */
    protected function _convertClassIdsToExtendedIds($classIds)
    {
        $classes = [];

        foreach($classIds as $classId) {
            $taxClass  = \Mage::getModel('tax/class')->load($classId);
            $classes[] = $this->_getParser()->createExtendedId('tax_class', 'class_name', 'id', $taxClass->getClassName());

            unset($taxClass);
        }

        return $classes;
    }
}
