<?php

namespace Nexway\SetupManager\Util\Processor\Action\Product;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * allow to create product
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Create extends AbstractAction
{
    /**
     * override generic _create method to handle correct product create
     *
     * @return bool
     */
    protected function _create()
    {
        $this->getParameters()->setModel('catalog/product');
        $data = $this->getParameters()->getData();
        $this->getParameters()->setData(self::F_DATA, $data);

        return parent::_create();
    }
}
