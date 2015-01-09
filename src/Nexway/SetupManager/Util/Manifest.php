<?php

namespace Nexway\SetupManager\Util;

use Nexway\SetupManager\Util\Helper\Processor;

class Manifest
{
    /**
     * Stores current configuration
     * @var \Varien_Simplexml_Config
     */
    protected $_config;

    /**
     * Loads SM manifest
     *
     * @param  \Varien_Object $path Path object
     * @return void
     */
    public function load(\Varien_Object $path)
    {
        $manifest = $path->getFull() . DS . 'manifest.xml';
        $xml      = new \Varien_Simplexml_Config();

        // return if manifest can't be loaded
        if (!$xml->loadFile($manifest)) {
            return;
        }

        if (!$this->isManifestValidForPath($xml, $path)) {
            return;
        }

        $this->_config = $this->_addDefaultProcessors($xml);
    }

    /**
     * @return \Varien_Simplexml_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param \Varien_Simplexml_Config $xml
     * @param \Varien_Object $path
     * @return bool
     */
    public function isManifestValidForPath(\Varien_Simplexml_Config $xml, \Varien_Object $path)
    {
        $allowedGroups = [];
        $groupsNode    = $xml->getNode('allowed_groups');
        if (!$groupsNode) {
            return false;
        }

        // get all allowed group for manifest
        foreach ($groupsNode->children() as $group) {
            if ($group->getName() === 'group') {
                $allowedGroups[] = (string)$group;
            }
        }

        $search  = $path->getDirectory() . DS . $path->getGroup();
        $pattern = "#{$search}[/]?$#";

        // skip if group is invalid (allowed groups from manifest)
        // or if config do not ends with configuration/[website]/[group]
        if (!in_array($path->getGroup(), $allowedGroups)
            || 1 !== preg_match($pattern, $path->getParamPath(), $matches)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param \Varien_Simplexml_Config $xml
     * @return \Varien_Simplexml_Config
     */
    protected function _addDefaultProcessors(\Varien_Simplexml_Config $xml)
    {
        $type = $xml->getNode('type');
        if (!$type) {
            return $xml;
        }

        // add default preprocessor common/<type>
        $preprocessors = $xml->getNode(Processor::PRE_PROCESSORS);
        if ($preprocessors) {
            $preprocessors->addChild('directory', 'common' . DS . $type);
            $xml->setNode(Processor::PRE_PROCESSORS, $preprocessors);
        } else {
            $xml->setNode(
                Processor::PRE_PROCESSORS . '/directory',
                'common' . DS . $type
            );
        }

        return $xml;
    }
}
