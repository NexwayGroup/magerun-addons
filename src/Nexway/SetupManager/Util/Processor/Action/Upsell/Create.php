<?php

namespace Nexway\SetupManager\Util\Processor\Action\Upsell;

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
     */
    protected function _create()
    {
        $this->getParameters()->setModel('nexway_upsell/upsell');

        $data = $this->getParameters()->getData(self::F_DATA);

        $data['upsell_products'] = $data['source_product_id'] . '::' . $data['target_product_id'];


        /** @var Nexway_Upsell_Model_Upsell $upsellModel */
        $upsellModel    = \Mage::getModel('nexway_upsell/upsell');

        /** @var Nexway_Upsell_Model_Options $optionsModel */
        $optionsModel   = \Mage::getModel('nexway_upsell/options');

        $data = $optionsModel->parseData($data);
        $upsellModel->setData($data);
        $upsellModel->save();

        $upsellId = $upsellModel->getId();
        $upsellModel->setUpsellId($upsellId);

        $optionsModel->setUpsellId($upsellId);
        $optionsModel->removeOptions($upsellId);
        $optionsModel->saveIncomingData();

        $product = \Mage::helper('nexway_upsell')->getTargetProduct($data['upsell_products']);
        $assetHelper = \Mage::helper('nexway_assets');

        if (isset($data['asset_target_product_name'])) {
            $assetHelper->saveAsset($data['asset_target_product_name'], $product, $upsellModel, 'name');
        }

        if (isset($data['asset_target_product_short_description'])) {
            $assetHelper->saveAsset(
                $data['asset_target_product_short_description'],
                $product,
                $upsellModel,
                'short_description'
            );
        }

        if (!$upsellModel->validateEntity()) {
            $upsellModel->setStatus(
                \Nexway_Upsell_Model_Source_Status::UPSELL_MISCONFIGURED
            );
            /** form data validation requires actual upsell id */
            $upsellModel->setUpsellId($upsellModel->getId());
            $upsellModel->save();
        }

        return true;
    }
}
