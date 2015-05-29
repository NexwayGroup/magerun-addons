<?php
use Nexway\SetupManager\Util\Helper\Utils;

/**
 * @category   Nexway
 * @package    Nexway_SetupManager
 * @author     Grzegorz Pawlik <gpawlik@nexway.com>
 */

class Nexway_SetupManager_Test_Helper_UtilsExtractTest extends PHPUnit_Framework_TestCase {

    public function test_createConfigForStore_should_expand_to_valid_conf()
    {
        $utils = new Utils();
        $conf = $utils->createConfigForStore('store:code/de_de_b2b#id',
            [
                'config_path' => 'config_val',
                'config_path1' => 'config_val1',
                'config_path2' => 'config_val2',
            ]
        );

        $this->assertEquals(
            [
                [
                    'store/createconfig',
                    'parameters'    => [
                        'key'       => 'config_path',
                        'value'     => 'config_val',
                        'store'     => 'store:code/de_de_b2b#id',
                    ]
                ],
                [
                    'store/createconfig',
                    'parameters'    => [
                        'key'       => 'config_path1',
                        'value'     => 'config_val1',
                        'store'     => 'store:code/de_de_b2b#id',
                    ]
                ],
                [
                    'store/createconfig',
                    'parameters'    => [
                        'key'       => 'config_path2',
                        'value'     => 'config_val2',
                        'store'     => 'store:code/de_de_b2b#id',
                    ]
                ],
            ],
            $conf
        );
    }

    public function test_createConfigForDefault_should_expand_to_valid_conf()
    {
        $utils = new Utils();
        $conf = $utils->createConfigForDefault([
            'abandonment/alert_identity/email_identity' => 'general'
        ]);

        $this->assertEquals(
            [
                [
                    'default/createconfig',
                    'parameters' => [
                        'key' => 'abandonment/alert_identity/email_identity',
                        'value' => 'general'
                    ]
                ]
            ],
            $conf
        );
    }

    public function test_createConfigForWebsite_should_expand_to_valid_conf()
    {
        $utils = new Utils();
        $conf = $utils->createConfigForWebsite('website:code/avast#id', [
            'abandonment/alert_identity/email_identity' => 'general'
        ]);

        $this->assertEquals(
            [
                [
                    'website/createconfig',
                    'parameters' => [
                        'key' => 'abandonment/alert_identity/email_identity',
                        'value' => 'general',
                        'store' => 'website:code/avast#id'
                    ]
                ]
            ],
            $conf
        );
    }


} 