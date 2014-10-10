<?php

namespace Nexway\Magento\Util\Processor\Action\Defaultscope;

use Nexway\Magento\Util\Processor\AbstractConfigurationAction;

/**
 * Use action default/createconfig
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Mariusz Tasak <mtasak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Truncateconfig extends AbstractConfigurationAction
{
    /**
     * @return bool
     */
    public function execute()
    {
        $this->setScope('default');
        $this->_prepare();

        /**
         * @var \Mage_Core_Model_Resource $resource
         */
        $resource = \Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('core/config_data');

        $deleteCondition = array(
            $writeConnection->quoteInto(
                'path = ?',
                $this->getParameters()->getKey()
            ),
        );

        $writeConnection->delete($table, $deleteCondition);

        return true;
    }
}
