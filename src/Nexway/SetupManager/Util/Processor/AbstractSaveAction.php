<?php

namespace Nexway\SetupManager\Util\Processor;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Michał Adamiak <madamiak@nexway.com>
 * @copyright    Copyright (c) 2014, Nexway
 */
abstract class AbstractSaveAction extends AbstractAction
{
    /**
     * value for point that [OK] or [ERROR] string will show up
     * @var int
     */
    protected $_infoAlign = 80;

    /**
     * number of changed database configuration entries
     * @var int
     */
    protected $_configurationCounter = 1;

    /**
     * contains errors messages when try to set configuration
     * @var string
     */
    protected $_errors = '';

    /**
     * contains warning messages when try to set configuration
     * @var string
     */
    protected $_warnings = '';

    /**
     * full configuration array that can be applied using load_config.php
     * @var array
     */
    protected $_completeConfiguration = [];

    /**
     * @return bool
     */
    public function execute()
    {
        $action   = $this->getAction();
        $success  = [];

        switch ($action) {
            case 'create':
            case 'delete':
            case 'update':
            case 'save':
                $method   = "_$action";
                $success  = $this->$method();
        }

        $configuration = var_export($this->_completeConfiguration, TRUE);
        $this->_saveConfigurationFile($configuration);
        $this->_showFinalMessages();

        return $success;
    }

    /**
     * @return int
     */
    public function getNumberOfConfigurations()
    {
        return $this->_configurationCounter;
    }

    /**
     * Generic "save" action handler
     * allow save configuration to file
     *
     * @return bool
     */
    protected function _save()
    {
        throw new \Exception('Each config must have own implementation to save!');
    }

    /**
     * add some colors to string
     *
     * @param string $string
     * @param string $type
     * @return string
     */
    protected function _colorizeString($string, $type)
    {
        if ($this->getParameters()->getColors()) {
            $type = NULL;
        }

        switch ($type) {
            case 'red':
                return "\033[0;31m" . $string . "\033[0m";

            case 'green':
                return "\033[0;32m" . $string . "\033[0m";

            case 'brown':
                return "\033[0;33m" . $string . "\033[0m";

            case 'red_label':
                return "\033[41m" . $string . "\033[0m";

            case 'brown_label':
                return "\033[43m" . $string . "\033[0m";

            default:
                return $string;
        }
    }

    /**
     * show formatted message about started method/action
     *
     * @param array $entry
     * @param integer $key
     */
    protected function _showActionMessage(array $entry, $key)
    {
        $message            = "[$key] ";
        $additionalMessage  = FALSE;

        if (isset($entry[0])) {
            $additionalMessage = $entry[0];
        }

        if (isset($entry['action'])) {
            $additionalMessage = $entry['action'];
        }

        if ($additionalMessage) {
            $message .= "Started action: $additionalMessage";
        }

        $length =  mb_strlen($message, 'UTF-8');
        $spaces = $this->_infoAlign - $length;

        for ($i = 1;$i <= $spaces; $i++) {
            $message .= ' ';
        }

        echo $message;
    }

    /**
     * truncate message if its too long
     *
     * @param string $message
     * @param integer $width
     * @param null|string $end
     * @return string
     */
    protected function _truncate($message, $width, $end = NULL)
    {
        return \Mage::helper('core/string')->truncate($message, $width, $end);
    }

    /**
     * save to file whole configuration array
     *
     * @param string $configuration
     */
    protected function _saveConfigurationFile($configuration)
    {
        $path = $this->getParameters()->getPath();

        if ($path) {
            $bool = @file_put_contents(
                $path,
                "<?php\n" . 'return ' . "\n$configuration;\n"
            );

            if ($bool) {
                echo $this->_colorizeString(
                    "\nConfiguration file has been saved.\n",
                    'green'
                );
            } else {
                $this->_errors  = $this->_colorizeString(
                    "\nUnable to save file {$path}\n",
                    'red'
                );
            }
        }
    }

    /**
     * show error messages if their was some
     * and information about success or fail apply configuration
     */
    protected function _showFinalMessages()
    {
        if ($this->_errors) {
            echo "\n" . $this->_colorizeString(
                    "There was some errors when getting configuration:",
                    'red_label'
                ) . "\n";
            echo $this->_colorizeString($this->_errors . "\n", 'red');
        }

        if ($this->_warnings) {
            echo "\n" . $this->_colorizeString(
                    "There was some warnings when getting configuration:",
                    'brown_label'
                ) . "\n";
            echo $this->_colorizeString($this->_warnings . "\n", 'brown');
        }

        if ($this->_configurationCounter > 1) {
            $total = $this->_colorizeString(--$this->_configurationCounter, 'green');
            echo "\nTotal get configurations: $total\n\n";
        } else {
            echo $this->_colorizeString("There was no configuration.\n\n", 'red');
        }
    }

    /**
     * crate error message and set it in variable
     *
     * @param \Exception $error
     * @param integer $key
     */
    protected function _prepareErrorMessage(\Exception $error, $key)
    {
        $this->_errors .= "[$key] message: {$error->getMessage()}\n";
    }

    /**
     * crate warning message and set it in variable
     *
     * @param string $warning
     * @param integer $key
     */
    protected function _prepareWarningMessage($warning, $key)
    {
        $this->_warnings .= "[$key] message: $warning\n";
    }
}
