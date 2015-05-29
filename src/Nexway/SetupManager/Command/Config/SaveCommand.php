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

class SaveCommand extends AbstractMagentoCommand
{
    /** @var InputInterface $input */
    protected $_input;

    /** @var OutputInterface $input */
    protected $_output;

    protected function configure()
    {
        $this
            ->setName('nexway:config:save')
            ->setDescription('Save configuration from magento instance to file.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'configuration type to save:
    all            - save all types (as path you need to specify the directory)
    cms            - save cms pages
    system         - save config for default, website and store view
    stores         - save websites ans store views
    block          - save static blocks
    agreement      - save agreements
    taxes          - save taxes  (tax class, tax rates and tax rules)
    taxclass       - save only tax classes
    taxrate        - save only tax rates
    taxrule        - save only tax rules
    featuredblock  - save featured blocks
                '
            )
            ->addArgument('path', InputArgument::REQUIRED, 'path to file where configuration will be saved')
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
        $this->_input = $input;
        $this->_output = $output;

        $path       = $this->_input->getArgument('path');
        $saveAction = $this->_input->getArgument('type');

        if ($this->_shouldSaveAll()) {
            $saveActions = [
                'stores'           => '00-initialize.php',
                'cms'              => '05-cms.php',
                'system'           => '10-config.php',
                'agreement'        => '30-agreement.php',
                'block'            => '30-block.php',
                'taxes'            => '40-taxes.php',
                'featuredblock'    => '50-featuredblock.php'
            ];
        } else {
            $saveActions = [
                $saveAction => $path,
            ];
        }

        if ($this->_shouldDeleteFile()) {
            unlink($path);
        }
        if ($this->_shouldCreateSaveDirectory()) {
            mkdir($path);
        }

        foreach ($saveActions as $action => $fileName) {
            $file = $fileName;

            if ($this->_shouldSaveAll()) {
                $file = !$path ? null : $path . '/' . $fileName;
            }

            $entry[] = [
                'action' => $action . '/save',
                'parameters' => [
                    'colors' => false,
                    'path' => $file,
                ]
            ];
        }

        /** @var Processor $processor */
        $processor = Processor::getInstance();

        try{
            $processor->processAll($entry);
        } catch (\Exception $error) {
            echo $error->getMessage() . "\n\n";
        }

        return true;
    }

    private function _shouldSaveAll()
    {
        return 'all' == $this->_input->getArgument('type');
    }

    private function _shouldDeleteFile()
    {
        return $this->_shouldSaveAll() && file_exists($this->_input->getArgument('path'));
    }

    private function _shouldCreateSaveDirectory()
    {
        return $this->_shouldSaveAll() && !file_exists($this->_input->getArgument('path'));
    }
}
