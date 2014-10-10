<?php
namespace Nexway\SetupManager\Util;
/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Christophe Eble <ceble@nexway.com>
 */
class Image extends \Varien_Object
{
    /**
     * Attribute code for media gallery
     */
    const GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Value returned when there's no image available
     */
    const GALLERY_NO_IMAGE = 'no_selection';

    /**
     * Allowed extensions for image
     *
     * @var array
     */
    protected $_supportedExtensions = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * Creates images from local filesystem path
     *
     * @param  \Varien_Object $path     Path passed by the shell
     * @param  string        $filename Filename
     * @return Image
     */
    public function fromLocalPath(\Varien_Object $path, $filename)
    {
        // Replace backslash by regular slash in file path (otherwise breaks the parser)
        $filename = str_replace('\\', '/', $filename);
        $imagePath = $path->getRoot() . DS . $filename;

        $parts = pathinfo($imagePath);

        $this->setData([
            'binary_data'    => file_get_contents($imagePath),
            'full_path'      => $imagePath,
            'relative_path'  => $path->getDirectory() . DS . $filename,
            'file_name'      => $parts['filename'],
            'file_extension' => $parts['extension']
        ]);

        // Retrieve binary contents
        $this->setId($this);

        return $this;
    }

    /**
     * Creates images on filesystem
     *
     * @throws \Exception
     * @return string
     */
    public function create()
    {
        // If chars are printable, this is probably not a binary
        if (ctype_print($this->getBinaryData()) || !$this->getBinaryData()) {
            throw new \Exception('The image content must be valid binary data');
        }

        $imageFileContent = $this->getBinaryData();
        $smTempDirTopLevel = \Mage::getBaseDir('var') . DS . 'sm';
        $smTempDir = $smTempDirTopLevel . DS . \Mage::helper('core')->uniqHash();
        $imageFileName = $this->_getFileName();

        $ioAdapter = new \Varien_Io_File();
        $ioAdapter->checkAndCreateFolder($smTempDir);
        $ioAdapter->open(array('path' => $smTempDir));
        $ioAdapter->write($imageFileName, $imageFileContent, 0666);
        unset($imageFileContent);

        // try to create Image object to check if image data is valid
        try {
            new \Varien_Image($smTempDir . DS . $imageFileName);
        } catch (\Exception $e) {
            $ioAdapter->rmdir($smTempDir, true);
            throw new \Exception($e->getMessage());
        }
        $product = $this->getProduct();

        if ($product->getId()) {
            // Delete existing image of the same type
            $this->_delete();
        }

        $imageFileUri = $this->_getMediaGallery()
            ->addImage($product, $smTempDir . DS . $imageFileName, $this->getTypes(), true);
        $ioAdapter->rmdir($smTempDir, true);

        if ($this->hasTypes()) {
            $this->_getMediaGallery()->setMediaAttribute($product, $this->getTypes(), $imageFileUri);
        }

        return $this;
    }

    /**
     * Product image delete
     */
    protected function _delete()
    {
        $media = \Mage::getModel('catalog/product_attribute_media_api');
        $images = $media->items($this->getProduct()->getId());

        foreach ($images as $image) {
            foreach ($this->getTypes() as $type) {
                if (in_array($type, $image['types'])) {
                    $media->remove($this->getProduct()->getId(), $image['file']);
                    break;
                }
            }
        }
    }

    /**
     * Retrieve media gallery
     *
     * @throws \Exception
     * @return \Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected function _getMediaGallery()
    {
        $attributes = $this->getProduct()->getTypeInstance(true)->getSetAttributes($this->getProduct());

        if (!isset($attributes[self::GALLERY_ATTRIBUTE_CODE])
            || !$attributes[self::GALLERY_ATTRIBUTE_CODE] instanceof \Mage_Eav_Model_Entity_Attribute_Abstract
        ) {
            throw new \Exception('Current product does not support images');
        }
        $galleryAttribute = $attributes[self::GALLERY_ATTRIBUTE_CODE];
        /** @var $mediaGallery \Mage_Catalog_Model_Product_Attribute_Backend_Media */
        $mediaGallery = $galleryAttribute->getBackend();
        return $mediaGallery;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _getFileName()
    {
        $fileName = 'image';
        if ($this->hasFileName()) {
            $fileName = $this->getFileName();
        }

        if (!in_array($this->getFileExtension(), $this->_supportedExtensions)) {
            throw new \Exception('Unsupported extension');
        }
        $fileName .=  '.' . $this->getFileExtension();

        return $fileName;
    }
}
