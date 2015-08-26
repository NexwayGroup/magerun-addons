<?php
namespace Nexway\SetupManager\Util\Helper;

use Nexway\SetupManager\Util\Image;
/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Łukasz Lach <llach@nexway.com>
 * @author      Michał Adamiak <madamiak@nexway.com>
 */
class Data
{
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Data();
        }

        return $instance;
    }

    private function __construct() {}

    /**
     * Main entry point for parsing and processing configuration file
     *
     * @param array $config
     * @return array
     */
    public function handle(array $config)
    {
        $result = $this->getProcessorHelper()->processAll($config);
        return $result;
    }

    /**
     * @return Nexway_SetupManager_Helper_Parser
     */
    public function getParserHelper()
    {
        return Parser::getInstance();
    }

    /**
     * @return Image
     */
    public function getImageModel()
    {
        return new Image;
    }
    
    /**
     * @return Mage_Core_Model_Store_Group
     */
    public function getStoreGroupModel()
    {
        return \Mage::getModel('core/store_group');
    }

    /**
     * @return \Mage_Core_Model_Store
     */
    public function getStoreModel()
    {
        return \Mage::getModel('core/store');
    }

    /**
     * @return \Mage_Catalog_Model_Category
     */
    public function getCategoryModel()
    {
        // @todo ask @llach why we use nexway_catalog
        return \Mage::getModel('nexway_catalog/category');
    }

    /**
     * @return \Nexway_Catalog_Helper_Product
     */
    public function getNexwayProductHelper()
    {
        // @todo ask @llach why we use nexway_catalog
        return \Mage::helper('nexway_catalog/product');
    }

    /**
     * @return \Mage_Catalog_Helper_Product
     */
    public function getProductHelper()
    {
        return \Mage::helper('catalog/product');
    }

    /**
     * @return \Mage_Core_Model_Website
     */
    public function getWebsiteModel()
    {
        return \Mage::getModel('core/website');
    }

    /**
     * @return \Mage_Core_Model_Website
     */
    public function getFeaturedBlockModel()
    {
        return \Mage::getModel('featured/blocks');
    }

    public function getTaxClassModel()
    {
        return \Mage::getModel('tax/class');
    }

    public function getTaxRuleModel()
    {
        return \Mage::getModel('tax/calculation_rule');
    }

    public function getTaxRateModel()
    {
        return \Mage::getModel('tax/calculation_rate');
    }

    /**
     * @return false|\Mage_Core_Model_Abstract
     */
    public function getAttributesModel()
    {
        return \Mage::getModel('eav/entity_attribute_set');
    }

    /**
     * @return false|\Mage_Checkout_Model_Agreement
     */
    public function getAgreementModel()
    {
        return \Mage::getModel('checkout/agreement');
    }

    /**
     * @return false|\Mage_Cms_Model_Page
     */
    public function getCmsModel()
    {
        return \Mage::getModel('cms/page');
    }

    /**
     * @return false|\Mage_Customer_Model_Group
     */
    public function getCustomerGroupModel()
    {
        return \Mage::getModel('customer/group');
    }

    /**
     * @return false|\Nexway_Upsell_Model_Interstitial_Page
     */
    public function getInterstitialPageModel()
    {
        return \Mage::getModel('nexway_upsell/interstitial_page');
    }
}
