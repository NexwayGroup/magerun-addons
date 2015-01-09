<?php
namespace Tests\Nexway\SetupManager\Util\Helper\Utils\VatUtilsTrait;


class VatUtilsTest extends \PHPUnit_Framework_TestCase {

    /** @var  TraitProxy */
    private $trait;
    public function setUp()
    {
        $this->trait = new TraitProxy();
    }

    public function test_generate_vat_rates_returns_array()
    {
        $this->assertTrue(
            is_array($this->trait->createVatRates(["DE" => 10])),
            'createVatRates didn\'t return array'
        );
    }

    public function test_creates_action_name()
    {
        $result = $this->trait->createVatRates(["DE" => 10]);
        $this->assertEquals(
            'taxrate/create',
            $result[0][0]
        );
    }

    public function test_creates_correct_country_code()
    {
        $result =  $this->trait->createVatRates(["XX" => 10]);
        $this->assertEquals(
            'XX',
            $this->getTaxCountryId($result[0])
        );
    }

    public function test_creates_correct_rate_field()
    {
        $result =  $this->trait->createVatRates(["XX" => 10]);
        $this->assertEquals(
            10,
            $this->extractFieldFromResult($result[0], 'rate')
        );
    }

    public function test_creates_two_entries_for_two_codes()
    {
        $result =  $this->trait->createVatRates(["XX" => 10, 'YY' => 15]);
        $this->assertEquals(
            2,
            count($result[0]),
            'didn\'t generate two entries'
        );
    }

    public function test_for_many_items_should_have_correct_country()
    {
        $result =  $this->trait->createVatRates(["XX" => 10, 'YY' => 15]);

        $this->assertEquals(
            "XX",
            $this->getTaxCountryId($result[0])
        );
        $this->assertEquals(
            "YY",
            $this->getTaxCountryId($result[1])
        );
    }

    public function test_for_many_items_should_have_correct_code()
    {
        $result =  $this->trait->createVatRates(["XX" => 10, 'YY' => 15]);

        $this->assertEquals(
            "XX",
            $this->extractFieldFromResult($result[0], "code")
        );
        $this->assertEquals(
            "YY",
            $this->extractFieldFromResult($result[1], "code")
        );
    }


    public function test_for_many_items_should_have_correct_rate()
    {
        $result =  $this->trait->createVatRates(["XX" => 10, 'YY' => 15]);

        $this->assertEquals(
            '10.0000',
            $this->getTaxRate($result[0])
        );
        $this->assertEquals(
            '15.0000',
            $this->getTaxRate($result[1])
        );
    }

    public function test_rates_are_in_good_format()
    {
        $result = $this->trait->createVatRates(["XX" => 10]);

        $this->assertRegExp(
            "/^[0-9]{2}\.[0-9]{4}$/",
            $this->getTaxRate($result[0])
        );
    }

    /**
     * @param $fieldName
     * @param $expectedValue
     * @dataProvider expectedDefaultValues
     */
    public function test_rates_have_default_values($fieldName, $expectedValue)
    {
        $result = $this->trait->createVatRates(["AA" => 15]);

        $this->assertEquals(
            $expectedValue,
            $this->extractFieldFromResult($result[0], $fieldName)
        );
    }
    public function expectedDefaultValues()
    {
        return [
            ['tax_region_id', "0"],
            ['tax_postcode', "*"],
            ['zip_is_range', null],
            ['zip_from', null],
            ['zip_to', null],
            ['title', []]
        ];
    }


    /**
     * @param $result
     * @return mixed
     */
    protected function getTaxCountryId($result)
    {
        return $this->extractFieldFromResult($result, 'tax_country_id');
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function getTaxRate($result)
    {
        return $this->extractFieldFromResult($result, 'rate');
    }

    protected function extractFieldFromResult($result, $fieldName)
    {
        return $result['parameters']['data'][$fieldName];
    }

}
 