<?php
use Nexway\SetupManager\Util\Helper\Utils;

/**
 * @category   Nexway
 * @package    Nexway_SetupManager
 * @author     Grzegorz Pawlik <gpawlik@nexway.com>
 */
class Nexway_SetupManager_Test_Helper_UtilsSqueezeRealLifeTest extends
    PHPUnit_Framework_TestCase
{

    public function test_squeezed_file_should_have_same_contents()
    {
        if(!defined('DS')) {
            define('DS', '/');
        }
        $dir = dirname(__FILE__) . DS . 'UtilsSqueezeRealLifeTest';
        $originalFile = $dir . DS . '10-config.php';
        $squeezedFile = $dir . DS . '10-config.squeezed.php';

        $original = require($originalFile);

        $utils = new Utils();

        file_put_contents($squeezedFile,
            $utils->squeeze($original)
        );

        $unsqueezed = require($squeezedFile);
        unlink($squeezedFile);

        $cmp_function = function ($a, $b) {
            if ($a[0] == $b[0]) {
                // sort by key if scope/blabla is equal
                return strcmp($a['parameters']['key'], $b['parameters']['key']);
            }
            return strcmp($a[0], $b[0]); // sort by scope/blabla
        };
        usort($original, $cmp_function);
        usort($unsqueezed, $cmp_function);

        $this->assertEquals($original, $unsqueezed, "Original and unsqueezed configs differ");

    }

} 