<?php

namespace Nexway\SetupManager\Util\Helper;

use Nexway\SetupManager\Util\Processor\Action;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Mariusz Tasak <mtasak@nexway.com>
 * @author      Łukasz Lach <llach@nexway.com>
 * @author      Christophe Eble <ceble@nexway.com>
 */
class Processor
{
    /**
     * Current paths
     *
     * @var \Varien_Object
     */
    protected $path;

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Processor();
        }

        return $instance;
    }

    private function __construct() {}

    /**
     * Sets base path of current setup
     *
     * @param \Varien_Object $path
     */
    public function setPath(\Varien_Object $path)
    {
        $this->path = $path;
    }

    /**
     * Gets paths of current setup
     *
     * @return \Varien_Object
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param array $config
     * @return array
     */
    public function processAll(array $config)
    {
        $result = [];
        foreach ($config as $key => $entry) {
            $result[$key] = $this->process($entry);
        }
        return $result;
    }

    /**
     * @param array|Action $entry
     * @return mixed
     */
    public function process($entry)
    {
        $parser = $this->_getHelper()->getParserHelper();
        $parser->setProcessor($this);

        $entry = $parser->parseExtendedId($entry);

        $handler = new Action();
        if (!($entry instanceof Action)) {
            $action = $handler->assign($entry);
        } else {
            $action = $entry;
        }

        return $action->execute();
    }

    /**
     * @return Data
     */
    protected function _getHelper()
    {
        return Data::getInstance();
    }
}
