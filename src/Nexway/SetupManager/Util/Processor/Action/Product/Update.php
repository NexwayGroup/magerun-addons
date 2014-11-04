<?php

namespace Nexway\SetupManager\Util\Processor\Action\Product;

use Nexway\SetupManager\Util\Processor\AbstractAction;
use Nexway\SetupManager\Util\Image;

/**
 * allow to create product
 *
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Grzegorz Pawlik <gpawlik@nexway.com>
 * @author      Christophe Eble <ceble@nexway.com>
 * @copyright   Copyright (c) 2014, Nexway
 */
class Update extends AbstractAction
{
    /**
     * Supported image attributes
     * @var array
     */
    protected $_supportedImageAttributes = [
        'image', 'small_image', 'thumbnail'
    ];

    /**
     * Update or create product
     *
     * updates product if can be found by SKU, creates new one otherwise
     * @return bool
     */
    public function execute()
    {
        $params = $this->getParameters()->getData();
        $data   = $params['data'];

        $model = \Mage::getModel('catalog/product');
        if (isset($data['store_id'])) {
            $model->setStoreId($data['store_id']);
        }

        if (isset($data['sku'])) {
            $entity = $model->loadByAttribute('sku', $data['sku']);

            // update product if can be find by sku
            if ($entity) {
                // manage "stock = no" fix
                // http://magento.stackexchange.com/questions/9682/problems-with-saving-new-product-stock-price
                $entity->setStockItem(
                    \Mage::getModel('cataloginventory/stock_item')->loadByProduct($entity->getId())
                );
                $this->_createImages($entity, $data);
                $this->getParameters()->setData(self::F_TARGET, $entity);
                return $this->_update();
            }
        }

        $this->_createImages($model, $data);
        $this->getParameters()->setData(self::F_MODEL, 'catalog/product');

        return $this->_create();
    }

    /**
     * Create images for both create/update mode
     *
     * Images having the same relative path are merged to avoid duplicates
     * and uses Magento native behavior for targetting multiple media attributes
     * with the same file.
     *
     * @param  \Mage_Catalog_Model_Product $product Product
     * @param  array                      $data    Data
     * @return void
     */
    protected function _createImages(\Mage_Catalog_Model_Product $product, array &$data)
    {
        $images = [];
        // Check assets and create them dynamically
        foreach ($this->_supportedImageAttributes as $imageAttribute) {
            if (
                isset($data[$imageAttribute]) &&
                $data[$imageAttribute] instanceof Image
            ) {
                $image = $data[$imageAttribute];
                $image->setProduct($product);
                if (isset($images[$image->getRelativePath()])) {
                    $target = $images[$image->getRelativePath()];
                    $types  = $target->getTypes();
                    array_push($types, $imageAttribute);
                    $target->setTypes($types);
                } else {
                    $image->setTypes([$imageAttribute]);
                    $images[$image->getRelativePath()] = $image;
                }
                // It's very important to unset attribute right after processing!
                unset($data[$imageAttribute]);
            }
        }

        // Loop through each images and create them
        foreach ($images as $image) {
            $image->create();
        }

        if (!$product->getId()) {
            $data = array_merge($product->getData(), $data);
        }

        // Must be set, otherwise breaks image processing seriously!
        $this->getParameters()->setData([
            'data' => $data
        ]);
    }
}
