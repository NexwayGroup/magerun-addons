<?php
/**
 * Created by PhpStorm.
 * User: pixel
 * Date: 26.08.15
 * Time: 12:56
 */

namespace Nexway\SetupManager\Util\Processor\Action\Interstitialpage;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Marcin Tasak <mtasak@nexway.com>
 * @copyright   Copyright (c) 2015, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     * @throws \Exception
     */
    protected function _create()
    {
        $this->getParameters()->setModel('nexway_upsell/interstitial_page');

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
        $target->saveWithBulkAssetsSave();

        return true;
    }
}
