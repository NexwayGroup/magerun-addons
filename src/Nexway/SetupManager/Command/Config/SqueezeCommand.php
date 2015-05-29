<?php

namespace Nexway\SetupManager\Command\Config;

use Nexway\SetupManager\Util\Helper\Utils;
use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SqueezeCommand extends AbstractMagentoCommand
{
    /** @var Utils */
    protected $helper = null;

    /** @var InputInterface $_input */
    protected $_input;

    /** @var OutputInterface $_output*/
    protected $_output;

    protected function configure()
    {
        $this
            ->setName('nexway:config:squeeze')
            ->setDescription('Squeeze config from file or directory.')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'path to configuration file/directory'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helper  = new Utils();
        $this->_input  = $input;
        $this->_output = $output;

        $configurationPath = $this->_input->getArgument('path');

        if (!file_exists($configurationPath)) {
            $this->_output->writeln(
                '<error>You must give at last one valid/accessible configuration file/directory.</error>'
            );
            return false;
        }

        // load configuration file
        if (is_file($configurationPath)) {
            $this->_squeezeFile($configurationPath);
        } else {
            $this->_squeezeDir($configurationPath);
        }

        return true;
    }

    /**
     * @param string $dir
     */
    protected function _squeezeDir($dir)
    {
        $list = '';
        $dir  = escapeshellarg($dir);
        exec('find ' . $dir . ' -type f | sort -n', $list);
        foreach ($list as $file) {
            $this->_squeezeFile($file);
        }
    }

    /**
     * @param string $path
     */
    protected function _squeezeFile($path)
    {
        try {
            $configuration = $this->_readFile($path);
            $output = $this->helper->squeeze($configuration);

            file_put_contents($path, $output);
            $this->_output->writeln('<info>Config Squeezed!</info>');
        } catch(\UnexpectedValueException $e) {
            $this->_output->writeln('<error>File doesn\'t contains valid config!</error>');
        }
        catch (\Exception $e) {
            $this->_output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     *
     * @param string $file
     * @return NULL|FALSE|array
     */
    protected function _readFile($file)
    {
        $this->_output->writeln('Load file path: ' . $file);

        if (!file_exists($file)) {
            $this->_output->writeln('<error>File: ' . $file . 'doesn\'t exists!</error>');

            return null;
        }

        // utils for skip fatal error in configuration files when config squeezed before
        $utils = new Utils();
        $data  = include($file);

        if (!$data) {
            $this->_output->writeln('<error>Including file: ' . $file . ' failed!</error>');

            return false;
        }

        return $data;
    }
}