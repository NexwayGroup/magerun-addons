<?php

namespace Nexway\SetupManager\Util\Processor\Action\Taxclass;

use Nexway\SetupManager\Util\Processor\AbstractSaveAction;

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
            $this->getTaxClassConfiguration(),
            $this->_completeConfiguration
        );

        return true;
    }

    /**
     * @return array
     */
    public function getTaxClassConfiguration()
    {
        $taxClasses = [];

        $taxClassCollection = \Mage::getModel('tax/class')->getCollection();

        foreach($taxClassCollection as $taxClass) {
            try{
                $this->_showActionMessage(
                    ['save tax class - ' . $taxClass->getClassName()],
                    $this->_configurationCounter
                );

                $data = $taxClass->getData();

                unset($data['class_id']);

                $taxClasses[] = [
                    'taxclass/create',
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

        return $taxClasses;
    }
}