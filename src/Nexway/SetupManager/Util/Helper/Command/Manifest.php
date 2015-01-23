<?php

namespace Nexway\SetupManager\Util\Helper\Command;

use Nexway\SetupManager\Util\Helper\Processor;
use Nexway\SetupManager\Util\Helper\Command\Path;

/**
 * Class Manifest
 * @package Nexway\SetupManager\Util\Helper\Command
 *
 * @author     Mariusz Tasak <mtasak@nexway.com>
 * @copyright  Copyright (c) 2015 Nexway
 */
class Manifest
{
    /**
     * Stores current configuration
     * @var \DOMDocument
     */
    protected $_config;

    /**
     * @var \Nexway\SetupManager\Util\Helper\Command\Path
     */
    protected $_path;

    /**
     * Loads SM manifest
     *
     * @param  Path $path Path object
     * @return void
     */
    public function load(Path $path)
    {
        $fileName = $path->getFull() . '/manifest.xml';
        $xml      = new \DOMDocument();

        // return if manifest can't be loaded
        if (!file_exists($fileName) || !$xml->load($fileName)) {
            return;
        }

        $this->_path = $path;

        if (!$this->isValidForPath($xml, $path)) {
            return;
        }

        $this->_config = $this->_addDefaultProcessors($xml);
    }

    /**
     * @return \DOMDocument
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Checks if group from path is declared in manifest.
     * Manifest is valid if path ends with: configuration/website/[group],
     * where group is declared in manifest.
     * 
     * @param \DOMDocument $xml
     * @param Path $path
     * @return bool
     */
    public function isValidForPath(\DOMDocument $xml, Path $path)
    {
        $allowedGroups = [];
        $groupsNode    = $xml->getElementsByTagName('allowed_groups');
        if (!$groupsNode->length) {
            return false;
        }

        // get all allowed group for manifest
        foreach($groupsNode->item(0)->childNodes as $group) {
            if ($group->nodeName === 'group') {
                $allowedGroups[] = (string)$group->textContent;
            }
        }

        $search  = $path->getDirectory() . '/' . $path->getGroup();
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
     * @param string $type postprocessors|preprocessors
     * @return array
     */
    public function getProcessors($type)
    {
        $processors = [];
        $config     = $this->getConfig();

        if (!$config) {
            return $processors;
        }

        $processorsNode = $config->getElementsByTagName($type);

        if (!$processorsNode->length) {
            return $processors;
        }

        foreach ($processorsNode->item(0)->childNodes as $processor) {
            if ($processor->nodeName == 'directory') {
                $processors[] = $processor->textContent;
            }
        }

        return $processors;
    }

    /**
     * @param \DOMDocument $xml
     * @return \DOMDocument
     */
    protected function _addDefaultProcessors(\DOMDocument $xml)
    {
        $type = $xml->getElementsByTagName('type');
        if (!$type->length) {
            return $xml;
        }

        // add default preprocessor before existing, common/<type>
        $preprocessors = $xml->getElementsByTagName(Processor::PRE_PROCESSORS);
        $websiteCommon = $xml->createElement('directory', 'common/' . $type->item(0)->textContent);
        $commonType    = $xml->createElement('directory', $this->_path->getWebsite() . '/common');

        if ($preprocessors->length) {
            $firstNode     = $preprocessors->item(0)->firstChild;
            $preprocessors->item(0)->insertBefore($commonType, $firstNode);
            $preprocessors->item(0)->insertBefore($websiteCommon, $firstNode);
        } else {
            $element = $xml->createElement('preprocessors');
            $element->appendChild($commonType);
            $element->appendChild($websiteCommon);
            $xml->appendChild($element);
        }

        return $xml;
    }
}
