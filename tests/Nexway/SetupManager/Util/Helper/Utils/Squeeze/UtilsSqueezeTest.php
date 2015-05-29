<?php
use Nexway\SetupManager\Util\Helper\Utils;

/**
 * @category   Nexway
 * @package    Nexway_SetupManager
 * @author     Grzegorz Pawlik <gpawlik@nexway.com>
 */
class Nexway_SetupManager_Test_Helper_UtilsSqueezeTest extends
    PHPUnit_Framework_TestCase
{
    /** @var  Utils */
    protected $utils;

    public function setUp()
    {
        $this->utils = new Utils();
    }

    public function test_should_squeeze_default_config()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'default/createconfig',
                    'parameters' => [
                        'key'   => 'key/config',
                        'value' => 'confval'
                    ]
                ]
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForDefault'
        );

        $this->assertEquals(['key/config' => 'confval'], $actual);
    }

    public function test_should_squeeze_default_config_with_many_entries()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'default/createconfig',
                    'parameters' => [
                        'key'   => 'key/config',
                        'value' => 'confval'
                    ]
                ],
                [
                    'default/createconfig',
                    'parameters' => [
                        'key'   => 'key/config2',
                        'value' => 'confval2'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForDefault'
        );

        $this->assertEquals(
            [
                'key/config' => 'confval',
                'key/config2' => 'confval2',
            ],
            $actual
        );
    }

    public function test_should_squeeze_config_and_not_die_because_of_single_quote()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'default/createconfig',
                    'parameters' => [
                        'key'   => 'key/config',
                        'value' => "confval'd"
                    ]
                ],
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'key/config',
                        'value' => "confval'd",
                        'store' => 'website1'
                    ]
                ],
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'key/config',
                        'value' => "confval'd",
                        'store' => 'store1'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod($squeezed, 'createConfigForDefault');
        $this->assertEquals(['key/config' => 'confval\'d'], $actual, "default not ok");

        $actual = $this->extractValuesFromMethod($squeezed, 'createConfigForWebsite', "'website1'");
        $this->assertEquals(['key/config' => 'confval\'d'], $actual, "website not ok");

        $actual = $this->extractValuesFromMethod($squeezed, 'createConfigForStore', "'store1'");
        $this->assertEquals(['key/config' => 'confval\'d'], $actual, "store not ok");
    }

    public function test_squeeze_should_work_when_only_create_config_for_store_exists()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'key/config',
                        'value' => "confval'd",
                        'store' => 'store1'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod($squeezed, 'createConfigForDefault');
        $this->assertEquals([], $actual, "default not ok");

        $actual = $this->extractValuesFromMethod($squeezed, 'createConfigForStore', "'store1'");
        $this->assertEquals(['key/config' => 'confval\'d'], $actual, "store not ok");
    }

    public function test_should_squeeze_website_config()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'webstiteConfpath',
                        'value' => 'websiteConfVal',
                        'store' => 'websiteSelector'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForWebsite',
            "'websiteSelector'"
        );

        $this->assertEquals(
            ['webstiteConfpath' => 'websiteConfVal'],
            $actual
        );
    }

    public function test_should_squeeze_website_config_with_more_entries()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'webstiteConfpath_1',
                        'value' => 'websiteConfVal_1',
                        'store' => 'websiteSelector'
                    ]
                ],
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'webstiteConfpath_2',
                        'value' => 'websiteConfVal_2',
                        'store' => 'websiteSelector'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForWebsite',
            "'websiteSelector'"
        );

        $this->assertEquals(
            [
                'webstiteConfpath_1' => 'websiteConfVal_1',
                'webstiteConfpath_2' => 'websiteConfVal_2',
            ],
            $actual
        );
    }

    public function test_should_squeeze_website_config_with_more_entries_and_many_websites()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'webstiteConfpath_1',
                        'value' => 'websiteConfVal_1',
                        'store' => 'websiteSelector_1'
                    ]
                ],
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'webstiteConfpath_2',
                        'value' => 'websiteConfVal_2',
                        'store' => 'websiteSelector_2'
                    ]
                ],
            ]
        );

        $website1 = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForWebsite',
            "'websiteSelector_1'"
        );

        $this->assertEquals(['webstiteConfpath_1' => 'websiteConfVal_1'], $website1);

        $website2 = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForWebsite',
            "'websiteSelector_2'"
        );

        $this->assertEquals(['webstiteConfpath_2' => 'websiteConfVal_2'], $website2);
    }

    public function test_should_squeeze_store_config()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath',
                        'value' => 'storeConfVal',
                        'store' => 'storeSelector'
                    ]
                ]
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForStore',
            "'storeSelector'"
        );

        $this->assertEquals(
            ['storeConfpath' => 'storeConfVal'],
            $actual
        );

    }

    public function test_combination_of_default_website_and_store()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'default/createconfig',
                    'parameters' => [
                        'key'   => 'confPath',
                        'value' => 'confVal',
                    ]
                ],
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath',
                        'value' => 'storeConfVal',
                        'store' => 'storeSelector'
                    ]
                ],
                [
                    'website/createconfig',
                    'parameters' => [
                        'key'   => 'websiteConfpath',
                        'value' => 'websiteConfVal',
                        'store' => 'websiteSelector'
                    ]
                ],
            ]
        );

        $default = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForDefault'
        );

        $this->assertEquals(
            ['confPath' => 'confVal'],
            $default
        );

        $website = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForWebsite',
            "'websiteSelector'"
        );

        $this->assertEquals(
            ['websiteConfpath' => 'websiteConfVal'],
            $website
        );

        $store = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForStore',
            "'storeSelector'"
        );

        $this->assertEquals(
            ['storeConfpath' => 'storeConfVal'],
            $store
        );
    }

    public function test_should_squeeze_store_with_many_config_entries()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath_1',
                        'value' => 'storeConfVal_1',
                        'store' => 'storeSelector'
                    ]
                ],
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath_2',
                        'value' => 'storeConfVal_2',
                        'store' => 'storeSelector'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForStore',
            "'storeSelector'"
        );

        $this->assertEquals(
            [
                'storeConfpath_1' => 'storeConfVal_1',
                'storeConfpath_2' => 'storeConfVal_2'
            ],
            $actual
        );

    }

    public function test_should_squeeze_store_with_many_config_entries_for_many_stores()
    {
        $squeezed = $this->utils->squeeze(
            [
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath_1',
                        'value' => 'storeConfVal_1',
                        'store' => 'storeSelector_1'
                    ]
                ],
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath_2',
                        'value' => 'storeConfVal_2',
                        'store' => 'storeSelector_2'
                    ]
                ],
            ]
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForStore',
            "'storeSelector_1'"
        );

        $this->assertEquals(
            [
                'storeConfpath_1' => 'storeConfVal_1',
            ],
            $actual
        );

        $actual = $this->extractValuesFromMethod(
            $squeezed,
            'createConfigForStore',
            "'storeSelector_2'"
        );

        $this->assertEquals(
            [
                'storeConfpath_2' => 'storeConfVal_2',
            ],
            $actual
        );

    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage squeeze can only operate on config entries
     */
    public function test_should_throw_when_not_config_entries_present()
    {
//        $this->markTestSkipped("I'll be back...");
        $this->utils->squeeze(
            [
                [
                    'store/createconfig',
                    'parameters' => [
                        'key'   => 'storeConfpath_1',
                        'value' => 'storeConfVal_1',
                        'store' => 'storeSelector'
                    ]
                ],
                [
                    'taxclas/create',
                    'parameters' => [
                        'key'   => 'storeConfpath_2',
                        'value' => 'storeConfVal_2',
                        'store' => 'storeSelector'
                    ]
                ],
            ]
        );
    }

    /**
     * extract method param from string as array
     *
     * used to test if we have proper value in string like
     *
     * return array_merge(
     *      $utils->createConfigForDefault([ THIS_WE_WANT ]),
     *      $utils->createConfigForWebsite('website/string', [THIS_WE_WANT_TOO]),
     *      $utils->createConfigForStore('store/string', [THIS_WE_WANT_AS_WELL])
     * );
     *
     * @param string $squeezed
     * @param string $methodName
     * @param string|null $firstParam of method, see line 3 and 4 in example
     * @return array
     */
    private function extractValuesFromMethod(
        $squeezed,
        $methodName,
        $firstParam = null
    ) {
        $firstParam .= $firstParam ? ", " : "";
        $pattern = '/'
            //. 'return array_merge\([\n]+.*'
            . '\s*\$utils->' . $methodName . '\(' . $firstParam . '(?<squeezed>.*?)\)[,]{0,1}\n'
            //. '\s*\)'
            . '/s'
        ;
        preg_match_all(
            $pattern,
            $squeezed,
            $matches
        );
        $toEvaluate = "return {$matches['squeezed'][0]};";
        $actual     = eval($toEvaluate);

        return $actual;
    }

} 