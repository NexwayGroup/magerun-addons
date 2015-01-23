<?php


namespace Tests\Nexway\SetupManager\Util\Helper\Command;

use Nexway\SetupManager\Util\Helper\Command\Path;

/**
 * Class PathTest
 * @package Tests\Nexway\SetupManager\Util\Helper\Command
 *
 * @author     Mariusz Tasak <mtasak@nexway.com>
 * @copyright  Copyright (c) 2015 Nexway
 * 
 * @coversDefaultClass Nexway\SetupManager\Util\Helper\Command\Path
 */
class PathTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }
    
    /**
     * @covers ::isValid
     * @covers ::_processPath
     */
    public function test_isValid_should_return_false_for_invalid_param_path()
    {
        $paramPath = 'asdasd/asd/asda/';
        $path = new Path($paramPath, 'whatever', 'whatever');

        $this->assertFalse($path->isValid());
    }

    /**
     * @covers ::isValid
     * @covers ::_processPath
     */
    public function test_isValid_should_return_true_for_valid_param_path()
    {
        $paramPath = 'configuration/whatever/whatever2/';
        $path = new Path($paramPath, 'whatever', 'whatever');

        $this->assertTrue($path->isValid());
    }

    /**
     * @covers ::getRoot
     */
    public function test_getRoot_should_return_value_passed_to_constructor()
    {
        $paramPath = 'configuration/whatever/whatever2/';
        $path = new Path($paramPath, 'whatever', 'rootdir');

        $this->assertEquals('rootdir', $path->getRoot());
    }

    /**
     * @covers ::getParamPath
     */
    public function test_getParamPath_should_return_value_passed_to_constructor()
    {
        $paramPath = 'configuration/param/path/';
        $path = new Path($paramPath, 'whatever', 'rootdir');

        $this->assertEquals($paramPath, $path->getParamPath());
    }

    /**
     * @covers ::getDirectory
     * @covers ::_processPath
     */
    public function test_getDirectory_should_return_proper_value()
    {
        $paramPath = 'configuration/eset/path/';
        $path = new Path($paramPath, 'whatever', 'rootdir');

        $this->assertEquals('configuration/eset', $path->getDirectory());
    }

    /**
     * @covers ::getFull
     * @covers ::_processPath
     */
    public function test_getFull_should_return_proper_value()
    {
        $paramPath = 'configuration/eset/path/';
        $path = new Path($paramPath, '/base/absolute', 'rootdir');

        $this->assertEquals('/base/absolute/configuration/eset', $path->getFull());
    }

    /**
     * @covers ::getGroup
     * @covers ::_processPath
     */
    public function test_getGroup_should_return_proper_value()
    {
        $paramPath = 'configuration/eset/prep/';
        $path = new Path($paramPath, '/base/absolute', 'rootdir');

        $this->assertEquals('prep', $path->getGroup());
    }

    /**
     * @covers ::getGroup
     * @covers ::_processPath
     */
    public function test_getWebsite_should_return_proper_value()
    {
        $paramPath = 'configuration/eset/prep/';
        $path = new Path($paramPath, '/base/absolute', 'rootdir');

        $this->assertEquals('eset', $path->getWebsite());
    }
}
