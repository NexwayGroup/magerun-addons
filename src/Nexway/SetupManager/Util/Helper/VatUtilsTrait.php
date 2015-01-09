<?php
/**
 * @category   Nexway
 * @package    Nexway_...
 * @author     Grzegorz Pawlik <gpawlik@nexway.com>
 */

namespace Nexway\SetupManager\Util\Helper;


trait VatUtilsTrait
{
    public function createVatRates($codes)
    {
        $returnArr = [];

        foreach ($codes as $country => $rate) {
            $returnArr[] = [
                'taxrate/create',
                'parameters' =>
                    [
                        'data' =>
                            [
                                'tax_country_id' => $country,
                                'code'           => $country,
                                'rate'           => sprintf("%01.4f", $rate),
                                'tax_region_id'  => "0",
                                'tax_postcode'   => "*",
                                'zip_is_range'   => null,
                                'zip_from'       => null,
                                'zip_to'         => null,
                                'title'          => [],
                            ]
                    ]
            ];
        }

        return $returnArr;
    }
} 