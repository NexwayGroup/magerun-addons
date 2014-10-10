<?php

namespace Nexway\SetupManager\Util\Processor\Action\Customergroup;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Mariusz Tasak
 * @copyright    Copyright (c) 2014, Nexway
 */
class Update extends AbstractAction
{
    /**
     * update or create customer group
     *
     * updates product if can be found by customer_group_code,
     * created new one otherwise
     * @return bool
     */
    public function execute()
    {
        $params = $this->getParameters();
        $data   = $params->getData();

        if (isset($data['customer_group_code'])) {
            $model  = \Mage::getModel('customer/group');
            $entity = $model->load($data['customer_group_code'], 'customer_group_code');
            if ($entity->getId()) {
                $this->getParameters()->setData(self::F_TARGET, $entity);

                return $this->_update();
            }
        }

        return $this->_create();
    }
}
