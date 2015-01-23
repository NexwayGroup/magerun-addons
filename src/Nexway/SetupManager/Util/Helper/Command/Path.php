<?php

namespace Nexway\SetupManager\Util\Helper\Command;

/**
 * Class Path
 * @package Nexway\SetupManager\Util\Helper\Command
 *
 * @author     Mariusz Tasak <mtasak@nexway.com>
 * @copyright  Copyright (c) 2015 Nexway
 */
class Path
{
    /** @var  string $_root */
    private $_root;
    
    /** @var  string $_root */
    private $_directory;
    
    /** @var  string $_root */
    private $_full;

    /** @var  string $_root */
    private $_group;
    
    /** @var  string $_website */
    private $_website;

    /** @var  string $_root */
    private $_paramPath;

    /** @var  string $_root */
    private $_baseAbsolutePath;

    /** @var  bool $_isValid */
    private $_isValid = false;

    public function __construct($paramPath, $baseAbsolutePath, $configRootDir)
    {
        $this->_root             = $configRootDir;
        $this->_paramPath        = $paramPath;
        $this->_baseAbsolutePath = $baseAbsolutePath;

        $this->_processPath();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * @return string
     */
    public function getFull()
    {
        return $this->_full;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    public function getWebsite()
    {
        return $this->_website;
    }

    /**
     * @return string
     */
    public function getParamPath()
    {
        return $this->_paramPath;
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->_root;
    }

    protected function _processPath()
    {
        // Resolve current base path for configuration
        preg_match('/(configuration\/([a-z_-]+))\/([a-z_-]+)/', $this->_paramPath, $basePath);

        if (!$basePath) {
            return;
        }

        $this->_isValid   = true;
        $this->_directory = $basePath[1];
        $this->_full      = $this->_baseAbsolutePath . '/' . $basePath[1];
        $this->_group     = $basePath[3];
        $this->_website   = $basePath[2];
    }
}
