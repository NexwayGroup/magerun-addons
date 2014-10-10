<?php

namespace Nexway\Magento\Util\Processor\Action\Taxes;

use Nexway\Magento\Util\Processor\Action\Taxclass\Save as SaveTaxclass;
use Nexway\Magento\Util\Processor\Action\Taxrate\Save as SaveTaxrate;
use Nexway\Magento\Util\Processor\Action\Taxrule\Save as SaveTaxrule;
use Nexway\Magento\Util\Processor\AbstractSaveAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * Save all taxes (tax class, tax rate and tax rule)
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Mariusz Tasak
 * @copyright    Copyright (c) 2014, Nexway
 */
class Save extends AbstractSaveAction
{
    /**
     * @var Parser
     */
    protected $_parser;

    /**
     * save all websites and stores
     */
    protected function _save()
    {
        $actionTaxClass = new SaveTaxclass();
        $actionTaxRate  = new SaveTaxrate();
        $actionTaxRules = new SaveTaxrule();

        $actionTaxClass->setParameters($this->getParameters());
        $actionTaxRate->setParameters($this->getParameters());
        $actionTaxRules->setParameters($this->getParameters());

        $this->_completeConfiguration = array_merge(
            $actionTaxClass->getTaxClassConfiguration(),
            $actionTaxRate->getTaxRateConfiguration(),
            $actionTaxRules->getTaxRuleConfiguration(),
            $this->_completeConfiguration
        );

        $this->_configurationCounter += $actionTaxClass->getNumberOfConfigurations();
        $this->_configurationCounter += $actionTaxRate->getNumberOfConfigurations();
        $this->_configurationCounter += $actionTaxRules->getNumberOfConfigurations();

        return true;
    }
}
