<?php

namespace Nexway\Magento\Util\Processor\Action\Cms;

use Nexway\Magento\Util\Processor\AbstractAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 *
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2013, Nexway
 */
class Update extends AbstractAction
{
    /**
     * @see Nexway_SetupManager_Model_Processor_AbstractAction::execute()
     */
    protected function _update()
    {
        $this->getParameters()->setModel('cms/page');
        /** @var Parser $configParser */
        $configParser   = Parser::getInstance();
        $data           = $this->getParameters()->getData(self::F_DATA);

        $data['content'] = $configParser->convert(
            $data['content'],
            Parser::CONVERT_FROM_EXTID
        );

        if (isset($data['parent_id']) && $data['parent_id'] === '0') {
            $loadId     = $data['store_id'];
            $loadName   = 'store_id';
            if (isset($data['url_key']) && empty($data['url_key'])) {
                $data['identifier'] = $data['url_key'];
            }
        } else {
            /** @var \Mage_Cms_Model_Page $collection */
            $collection   = \Mage::getModel('cms/page')->getCollection()->addFieldToFilter(
                'identifier',
                [
                    'eq' => $data['url_key'],
                ]
            )->addFieldToFilter(
                'store_id',
                [
                    'eq' => $data['store_id'],
                ]
            );

            $loadedData = $collection->getData()[0];
            $loadId     = $loadedData['page_id'];
            $loadName   = 'page_id';
        }

        /** @var \Mage_Cms_Model_Page $cmsModel */
        $cmsModel   = \Mage::getModel('cms/page')->load($loadId, $loadName);
        $data       = array_merge($cmsModel->getData(), $data);

        $cmsModel->setData($data);
        $cmsModel->save();

        return true;
    }
}
