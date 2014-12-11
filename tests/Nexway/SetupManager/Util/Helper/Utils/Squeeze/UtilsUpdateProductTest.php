<?php
use Nexway\SetupManager\Util\Helper\Utils;

 /**
 * @category   Nexway
 * @package    Nexway_SetupManager
 * @author     Christophe Eble <ceble@nexway.com>
 */
class Nexway_SetupManager_Test_Helper_UtilsUpdateProductTest extends PHPUnit_Framework_TestCase {

    public function test_updateProductForStore_should_expand_to_valid_conf()
    {
        $utils = new Utils();
        $conf = $utils->updateProductForStore('store:code/ch_fr#id', 'backup-dvd',
            [
                'description'           => 'Backup DVD',
                'short_description'     => 'Backup DVD'
            ]
        );

        $this->assertEquals(
            [
                [
                    'product/update',
                    'parameters'    => [
                        'data' => [
                            'description'       => 'Backup DVD',
                            'short_description' => 'Backup DVD',
                            'sku'               => 'backup-dvd',
                            'store_id'          => 'store:code/ch_fr#id'
                        ]
                    ]
                ]
            ],
            $conf
        );
    }

    public function test_updateProductForStore_with_multiple_storeviews_should_expand_to_valid_conf()
    {
        $utils = new Utils();
        $conf = $utils->updateProductForStore(['store:code/ch_fr#id', 'store:code/at_de#id'], 'backup-dvd',
            [
                'description'           => 'Backup DVD',
                'short_description'     => 'Backup DVD'
            ]
        );

        $this->assertEquals(
            [
                [
                    'product/update',
                    'parameters'    => [
                        'data' => [
                            'description'       => 'Backup DVD',
                            'short_description' => 'Backup DVD',
                            'sku'               => 'backup-dvd',
                            'store_id'          => 'store:code/ch_fr#id'
                        ]
                    ]
                ],
                [
                    'product/update',
                    'parameters'    => [
                        'data' => [
                            'description'       => 'Backup DVD',
                            'short_description' => 'Backup DVD',
                            'sku'               => 'backup-dvd',
                            'store_id'          => 'store:code/at_de#id'
                        ]
                    ]
                ],
            ],
            $conf
        );
    }
}