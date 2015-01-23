<?php


namespace Tests\Nexway\SetupManager\Util\Helper\Command;

use Nexway\SetupManager\Util\Helper\Command\Manifest;
use Nexway\SetupManager\Util\Helper\Command\Path;
use Nexway\SetupManager\Util\Helper\Processor;

/**
 * Class ManifestTest
 * @package Tests\Nexway\SetupManager\Util\Helper\Command
 *
 * @author     Mariusz Tasak <mtasak@nexway.com>
 * @copyright  Copyright (c) 2015 Nexway
 * 
 * @coversDefaultClass Nexway\SetupManager\Util\Helper\Command\Manifest
 */
class ManifestTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     * @covers ::load
     * @covers ::_addDefaultProcessors
     */
    public function load_should_create_node_preprocessors_and_add_preprocessors()
    {
        $manifest = new Manifest();
        $manifest->load($this->_getPath('notexistingpreprocessors'));

        $expected = ['eset/common', 'common/checkout'];
        $this->assertEquals(
            $expected,
            $manifest->getProcessors(Processor::PRE_PROCESSORS)
        );
    }

    /**
     * @test
     * @covers ::load
     * @covers ::_addDefaultProcessors
     */
    public function load_should_prepend_preprocessors_to_existing()
    {
        $manifest = new Manifest();
        $manifest->load($this->_getPath('existingpreprocessors'));

        $expected = ['eset/common', 'common/checkout', 'pre/one', 'pre/two'];
        $this->assertEquals(
            $expected,
            $manifest->getProcessors(Processor::PRE_PROCESSORS)
        );
    }

    /**
     * @test
     * @covers ::load
     * @covers ::_addDefaultProcessors
     */
    public function load_should_prepend_preprocessors_if_node_exist_but_is_empty()
    {
        $manifest = new Manifest();
        $manifest->load($this->_getPath('emptypreprocessors'));

        $expected = ['eset/common', 'common/checkout'];
        $this->assertEquals(
            $expected,
            $manifest->getProcessors(Processor::PRE_PROCESSORS)
        );
    }

    /**
     * @test
     * @covers ::getProcessors
     */
    public function getProcessors_should_return_two_preprocessors()
    {
        $manifest = $this->_getManifestForProcessorsTest();
        $expected = ['pre/one', 'pre/two'];

        $this->assertEquals(
            $expected,
            $manifest->getProcessors(Processor::PRE_PROCESSORS)
        );
    }

    /**
     * @test
     * @covers ::getProcessors
     */
    public function getProcessors_should_return_one_postprocessors()
    {
        $manifest = $this->_getManifestForProcessorsTest();
        $expected = ['post/one'];

        $this->assertEquals(
            $expected,
            $manifest->getProcessors(Processor::POST_PROCESSORS)
        );
    }

    /**
     * @test
     * @dataProvider emptyPostProcessors
     * @covers ::getProcessors
     */
    public function getProcessors_should_not_return_postprocessors($xmlString)
    {
        $manifest = $this->_getManifestForProcessorsTest($xmlString);
        $expected = [];

        $this->assertEquals(
            $expected,
            $manifest->getProcessors(Processor::PRE_PROCESSORS)
        );
    }

    /**
     * @test
     * @covers ::load
     * @covers ::getConfig
     */
    public function getConfig_should_return_null_if_manifest_file_not_exists()
    {
        $stub = \Mockery::mock('Nexway\SetupManager\Util\Helper\Command\Path')->makePartial();
        $stub->shouldReceive(['getFull' => 'fake/path']);

        $manifest = new Manifest();
        $manifest->load($stub);

        $this->assertNull($manifest->getConfig());
    }

    /**
     * @test
     * @covers ::isValidForPath
     */
    public function isValidForPath_should_return_false_when_allowed_groups_node_not_exists()
    {
        $manifest = new Manifest();
        $manifest->load($this->_getPath());

        $xml = new \DOMDocument();
        $xml->loadXML('<config></config>');
        $result = $manifest->isValidForPath($xml, $this->_getPath());
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::isValidForPath
     */
    public function isValidForPath_should_return_false_when_allowed_groups_does_not_contains_current_group()
    {
        $manifest = new Manifest();
        $manifest->load($this->_getPath());

        $xml = new \DOMDocument();
        $xml->loadXML('<config><allowed_groups><group>prod</group></allowed_groups></config>');
        $result = $manifest->isValidForPath($xml, $this->_getPath());
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::isValidForPath
     */
    public function isValidForPath_should_return_true_when_allowed_groups_contains_current_group()
    {
        $manifest = new Manifest();
        $manifest->load($this->_getPath());

        $xml = new \DOMDocument();
        $xml->loadXML('<config><allowed_groups><group>prep</group></allowed_groups></config>');
        $result = $manifest->isValidForPath($xml, $this->_getPath());
        $this->assertTrue($result);
    }

    public function emptyPostProcessors()
    {
        return [
            ['<config></config>'],
            ['<config><postprocessors></postprocessors></config>'],
        ];
    }

    protected function _getManifestForProcessorsTest($xmlString = '')
    {
        $xmlString = $xmlString ? $xmlString : $this->_getSampleManifest();

        $xml = new \DOMDocument();
        $xml->loadXML($xmlString);

        $manifest = \Mockery::mock('Nexway\SetupManager\Util\Helper\Command\Manifest')->makePartial();
        $manifest->shouldReceive(['getConfig'  => $xml, 'isValidForPath' => true]);

        return $manifest;
    }

    protected function _getPath($manifestDirectory = '')
    {
        $stub = \Mockery::mock('Nexway\SetupManager\Util\Helper\Command\Path')->makePartial();
        $full = $manifestDirectory ? __DIR__ . '/Manifest/' .$manifestDirectory : __DIR__ . '/Manifest' ;

        $stub->shouldReceive(
            [
                'getParamPath' => '/configuration/eset/prep',
                'getFull' => $full,
                'getRoot' => __DIR__ . '/configuration',
                'getDirectory' => 'configuration/eset',
                'getGroup' => 'prep',
                'getWebsite' => 'eset'
            ]
        );

        return $stub;
    }

    /**
     * @return string
     */
    protected function _getSampleManifest()
    {
        return <<<MANIFEST
<config>
    <type>checkout</type>
    <allowed_groups>
        <group>prod</group>
        <group>prep</group>
    </allowed_groups>
    <preprocessors>
        <directory>pre/one</directory>
        <directory>pre/two</directory>
    </preprocessors>
    <postprocessors>
        <directory>post/one</directory>
    </postprocessors>
</config>
MANIFEST;
    }
}
