<?php

namespace Nexway\SetupManager\Util\Processor;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Łukasz Lach <llach@nexway.com>
 *              Mariusz Tasak <mtasak@nexway.com>
 *              Michał Adamiak <madamiak@nexway.com>
 * @method \Varien_Object setParameters
 * @method \Varien_Object getParameters
 */
abstract class AbstractAction extends \Varien_Object
{
    const F_MODEL  = 'model';
    const F_DATA   = 'data';
    const F_TARGET = 'target';

    /**
     * @return bool
     */
    public function execute()
    {
        $action = $this->getAction();
        switch ($action) {
            case 'create':
            case 'delete':
            case 'deleteall':
            case 'update':
                $method = "_$action";

                return $this->$method();
        }

        throw new \Exception('Incorrect action: ' . $action);
    }

    /**
     * Generic "create" action handler
     *
     * @return bool
     */
    protected function _create()
    {
        $model = $this->getParameters()->getData(self::F_MODEL);
        $data = $this->getParameters()->getData(self::F_DATA);
        $target = \Mage::getModel($model);

        if (!is_object($target)) {
            throw new \Exception('Invalid model: ' . $model);
        }

        if (!is_array($data)) {
            throw new \Exception('Data is not an array: ' . $data);
        }

        $target->setData($data);
        $target->save();

        return true;
    }

    /**
     * Generic "delete" action handler
     *
     * @return bool
     */
    protected function _delete()
    {
        $target = $this->getParameters()->getData(self::F_TARGET);

        if (!is_object($target)) {
            throw new \Exception('Target is not an object: ' . $target);
        }

        if (null === $target->getId()) {
            throw new \Exception('Target id is NULL');
        }

        $target->delete();

        return true;
    }

    /**
     * Generic "delete all" action handler
     *
     * @return bool
     */
    protected function _deleteall()
    {
        $model = $this->getParameters()->getData(self::F_MODEL);
        $target = \Mage::getModel($model);

        if (!is_object($target)) {
            throw new \Exception('Invalid model: ' . $model);
        }

        $collection = $target->getCollection();
        foreach ($collection as $item) {
            $item->delete();
        }

        return true;
    }

    /**
     * Generic "update" action handler
     *
     * @return bool
     */
    protected function _update()
    {
        $target = $this->getParameters()->getData(self::F_TARGET);
        $data = $this->getParameters()->getData(self::F_DATA);

        if (!is_object($target)) {
            throw new \Exception('Target is not an object: ' . $target);
        }

        if (null === $target->getId()) {
            throw new \Exception('Target id is NULL');
        }

        if (!is_array($data)) {
            throw new \Exception('Data is not an array: ' . $data);
        }

        $data = array_merge($target->getData(), $data);

        $target->setData($data);
        $target->save();

        return true;
    }
}
