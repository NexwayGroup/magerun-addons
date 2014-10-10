<?php

namespace Nexway\SetupManager\Command\Config;

use Nexway\SetupManager\Util\Helper\Parser;
use Nexway\SetupManager\Util\Helper\Processor;
use Nexway\SetupManager\Util\Helper\Utils;
use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends AbstractMagentoCommand
{
    /**
     * Base directory for configuration
     * @const string
     */
    const BASE_DIRECTORY = 'configuration';

    /**
     * Preprocessors node
     * @const string
     */
    const PRE_PROCESSORS = 'preprocessors';

    /**
     * Postprocessors node
     * @const string
     */
    const POST_PROCESSORS = 'postprocessors';

    /**
     * value for point that [OK] or [ERROR] string will show up
     * @var int
     */
    protected $_infoAlign = 60;

    /**
     * number of changed database configuration entries
     * @var int
     */
    protected $_configurationCounter = 0;

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
     * if true will load keys from blacklist
     *
     * @var bool
     */
    protected $_forceIgnoredPaths = false;

    protected $_baseDirectory = '';

    /**
     * Stores current configuration
     * @var \Varien_Simplexml_Config
     */
    protected $_config;

    /** @var InputInterface $input */
    protected $_input;

    /** @var OutputInterface $input */
    protected $_output;

    /**
     * Stores processor instance
     */
    protected $_processor;

    protected function configure()
    {
        $this
            ->setName('nexway:config:load')
            ->setDescription('Loads config from file to magento instance.')
            ->addArgument('path', InputArgument::REQUIRED, 'path to configuration file/directory')
            ->addOption(
                'ext-ids',
                null,
                InputOption::VALUE_NONE,
                'Print extended ids'
            )
            ->addOption(
                'ignore-manifest',
                null,
                InputOption::VALUE_NONE,
                'Do not load manifest file'
            )
            ->addOption(
                'hide-success-msg',
                null,
                InputOption::VALUE_NONE,
                'Do not print success messages, only errors and warnings'
            )
            ->addOption(
                'with-ignored-path',
                null,
                InputOption::VALUE_NONE,
                'Will force load all configuration given in blacklist'
            )
        ;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return version_compare($this->getApplication()->getVersion(), '1.74.1', '>=');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        if ($this->initMagento()) {
            
            $this->_input = $input;
            $this->_output = $output;

            // Set base directory
            $this->_baseDirectory = \Mage::getBaseDir() . DS . self::BASE_DIRECTORY;

            $this->_initProcessor();

            if ($input->getOption('ext-ids')) {
                $this->_printExtendedIds($output);
                return true;
            }

            $configurationPath = $this->_input->getArgument('path');

            if (!file_exists($configurationPath)) {
                $this->_output->writeln('<error>You must give at last one valid/accessible configuration file/directory.</error>');
                return false;
            }

            // load configuration file
            if (is_file($configurationPath)) {
                $this->_loadFile($configurationPath);
                if (!$this->_errors) {
                    $this->_runProcessors(self::POST_PROCESSORS);
                }
            } else {
                $this->_processDirectory($configurationPath);
                if (!$this->_errors) {
                    $this->_runProcessors(self::POST_PROCESSORS);
                }
            }

            $this->_showFinalMessages();
            return true;
        }
        
        return true;
    }

    /**
     * @return void
     */
    public function _printExtendedIds(OutputInterface $output)
    {
        $parser = Parser::getInstance();
        $plainExtIds = $parser->getAllowedExtendedIds();
        $compoundExtIds = $parser->getAllowedCompoundExtendedIds();
        $extIds = array_merge($plainExtIds, $compoundExtIds);
        $longestString = max(array_map('strlen', array_keys($extIds)));

        $output->writeln('Available extended ids');
        $output->writeln(str_pad('scope', $longestString) . "\t: field,field,(compound_field,compound_field),...");
        $output->writeln('---');
        foreach ($extIds as $field => $values) {
            echo str_pad($field, $longestString) . "\t: ";
            foreach ($values as $value) {
                $output->write((is_array($value) ? '(' . implode(',', $value) . ')' : $value) . ',');
            }
            $output->writeln('');
        }
    }

    /**
     * Inits processor and run preprocessor tasks if any
     *
     * @return void
     */
    protected function _initProcessor()
    {
        $this->_processor = Processor::getInstance();
        $path = $this->_input->getArgument('path');

        // If path cannot be loaded, exit immediately
        if (!$path) {
            return;
        }

        // Resolve current base path for configuration
        preg_match('/(configuration\/[a-z_-]+)\//', $path, $basePath);
        if ($basePath) {
            $basePath = $basePath[1];
            $path = new \Varien_Object([
                'root'  => $this->_baseDirectory,
                'directory' => $basePath,
                'full'  => getcwd() . DS . $basePath
            ]);

            $this->_processor->setPath($path);

            if (!$this->_input->getOption('ignore-manifest')) {
                $this->_loadManifest($path);
                // Check if we've some preprocessor task to complete
                $this->_runProcessors(self::PRE_PROCESSORS);
            }
        }
    }

    /**
     * Directory processor
     *
     * @param  string $dir Directory path
     * @return void
     */
    protected function _processDirectory($dir)
    {
        $list = '';
        $dir = escapeshellarg($dir);
        exec('find ' . $dir . ' -type f -name \'*.php\' | sort -V', $list);
        foreach ($list as $file) {
            $this->_loadFile($file);
        }
    }

    /**
     * Loads SM manifest
     *
     * @param  \Varien_Object $path Path object
     * @return void
     */
    protected function _loadManifest(\Varien_Object $path)
    {
        $manifest = $path->getFull() . DS . 'manifest.xml';
        $xml = new \Varien_Simplexml_Config();

        if ($xml->loadFile($manifest)) {
            $this->_config = $xml;
        }
    }

    /**
     * Runs a specific processor (preprocessor/postprocessor)
     *
     * @param  string $processor Processor name
     * @return void
     */
    protected function _runProcessors($processor = self::PRE_PROCESSORS)
    {
        if (!$this->_config) {
            return;
        }

        $processorsNode  = $this->_config->getNode($processor);
        if ($processorsNode) {
            // List all preprocess
            foreach ($processorsNode->children() as $processor) {
                if ($processor->getName() === 'directory') {
                    $directory = $this->_baseDirectory . DS . (string) $processor;
                    // Process directory
                    $this->_processDirectory($directory);
                }
            }
        }
    }

    protected function _loadFile($path)
    {
        $configuration  = $this->_readFile($path);
        $blackList      = $this->_readFile(getcwd() . '/configuration/blacklist.php');
        $error          = false;

        foreach ($configuration as $key => $entry) {

            $skipConfig = isset($entry['parameters']['key']) && in_array($entry['parameters']['key'], $blackList);
            if ($skipConfig && !$this->_input->getOption('with-ignored-path')) {
                $this->_showActionMessage($entry, $key);
                $this->_output->writeln('<comment>[WARNING]</comment>');
                $message = $entry['parameters']['key'] . ' - on blacklist';
                $this->_prepareWarningMessage($message, $key);
                $this->_configurationCounter++;

                continue;
            }

            try {
                $result = $this->_processor->process($entry);

                /** @var Parser $parser */
                $parser = Parser::getInstance();
                if ($parser->warning) {
                    $this->_showActionMessage($entry, $key);
                    $this->_output->writeln('<comment>[WARNING]</comment>');
                    $this->_prepareWarningMessage($parser->warning, $key);
                    $this->_configurationCounter++;
                } else if (!$result) {
                    $this->_showActionMessage($entry, $key);
                    $this->_output->writeln('<error>[ERROR]</error>');
                    $this->_errors .= "[$key] Something wrong when try to apply configuration\n";
                } else {
                    if (!$this->_input->getOption('hide-success-msg')) {
                        $this->_showActionMessage($entry, $key);
                        $this->_output->writeln('<info>[OK]</info>');
                    }
                    $this->_configurationCounter++;
                }

            } catch (\Exception $error) {
                $this->_showActionMessage($entry, $key);
                $this->_output->writeln('<error>[ERROR]</error>');
                $this->_prepareErrorMessage($error, $key);
            } finally {
                $key++;
            }
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

        $length =  strlen($message);
        $spaces = $this->_infoAlign - $length;

        for ($i = 1;$i <= $spaces; $i++) {
            $message .= ' ';
        }

        $this->_output->write($message);
    }

    /**
     * show error messages if their was some
     * and information about success or fail apply configuration
     */
    protected function _showFinalMessages()
    {
        if ($this->_errors) {
            $this->_output->writeln(
                "<fg=red>There was some errors on setting configuration: \n{$this->_errors}</fg=red>"
            );
        }

        if ($this->_warnings) {
            $this->_output->writeln(
                "<comment>There was some warnings on setting configuration: \n{$this->_warnings}</comment>"
            );
        }

        if ($this->_configurationCounter > 0) {
            $this->_output->writeln(
                "<info>Configuration has been applied</info>"
            );

            $this->_output->writeln(
                "<info>Total changed configurations: {$this->_configurationCounter}</info>"
            );
        } else {
            $this->_output->writeln(
                "<error>There was no configuration applied.</error>"
            );
        }
    }

    /**
     * check if file exists and return configuration array
     *
     * @param string $file
     * @return NULL|FALSE|array
     */
    protected function _readFile($file)
    {
        $this->_output->writeln("File path: {$file}");

        if (!file_exists($file)) {
            $this->_output->writeln(
                "<error>File: $file doesn't exists.</error>"
            );
            return NULL;
        }

        $utils = Utils::getInstance();
        $data = include($file);

        if (!$data) {
            $this->_output->writeln(
                "<error>Some error when including file</error>"
            );
            return FALSE;
        }

        return $data;
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