<?php

namespace Nexway\Magento\Util\Processor\Action\Cms;

use Nexway\Magento\Util\Processor\AbstractSaveAction;
use Nexway\Magento\Util\Helper\Parser;

/**
 * allow to create CMS pages
 * 
 * @category     Nexway
 * @package      Nexway_SetupManager
 * @author       Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
class Save extends AbstractSaveAction
{
    /**
     * allow to save configuration array for all existing in database cms pages
     * with their configuration
     */
    protected function _save()
    {
        /** @var Parser $configParser */
        $configParser      = Parser::getInstance();
        $cmsPageCollection = \Mage::getModel('cms/page')->getCollection();
        $parent            = [];
        $children          = [];

        foreach ($cmsPageCollection as $collection) {
            try {
                $data                         = $collection->getData();
                $configurationAction          = 'cms/create';
                $messageAction                = 'cms/save/'
                    . $this->_truncate(
                        $data['title'], 40, '...'
                    );
                $this->_showActionMessage(
                    [$messageAction],
                    $this->_configurationCounter
                );
                $code = \Mage::getModel('core/store')
                    ->load($data['store_id'])
                    ->getCode();

                $data['store_id'] = $configParser->createExtendedId(
                    'store',
                    'code',
                    'id',
                    $code
                );
                $data['url_key'] = $data['identifier'];
                unset($data['page_id']);
                unset($data['identifier']);
                unset($data['path']);
                $data['content'] = $configParser->convert($data['content']);

                $configuration = [
                    'action'        => $configurationAction,
                    'parameters'    => [
                        'data'      => $data,
                    ]
                ];

                if ($data['parent_id'] === '0') {
                    $configuration['action'] = 'cms/update';
                    $parent[]                = $configuration;
                } else {
                    unset($configuration['parameters']['data']['parent_id']);
                    $children[] = $configuration;
                }

                echo $this->_colorizeString("[OK]\n", 'green');

            } catch (\Exception $error) {
                echo $this->_colorizeString("[ERROR]\n", 'red');
                $this->_prepareErrorMessage($error,  $this->_configurationCounter);
            }

            $this->_configurationCounter++;
        }

        $cmsPages = array_merge($parent, $children);
        $this->_completeConfiguration = array_merge(
            $cmsPages,
            $this->_completeConfiguration
        );

        return true;
    }
}
