<?php

namespace Nexway\SetupManager\Util\Processor\Action\Group;

use Nexway\SetupManager\Util\Processor\AbstractConfigurationAction;

/**
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Updategroup extends AbstractConfigurationAction
{
    /**
     * @return bool
     */
    public function execute()
    {
        /** @var \Mage_Core_Model_Store_Group $model */
        $model = \Mage::getModel('core/store_group')->load(
            $this->getParameters()->getGroup()
        );

        $model->setRootCategoryId(
            $this->getParameters()->getRootCategory()
        )->save();

        return true;
    }
}
