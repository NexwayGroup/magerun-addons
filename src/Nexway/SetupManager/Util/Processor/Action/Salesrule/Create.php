<?php

namespace Nexway\SetupManager\Util\Processor\Action\Salesrule;

use Nexway\SetupManager\Util\Processor\AbstractAction;

/**
 * Minimal example
 * [
 *      'action' => 'salesrule/create',
 *      'parameters' =>
 *      [
 *          'data' => [],
 *      ],
 *  ]
 *
 * Simple example
 * [
 *      'action' => 'salesrule/create',
 *      'parameters' =>
 *      [
 *          'data' =>
 *          [
 *              'name' => 'Marcin Rogacki',
 *              'is_active' => '1',
 *              'is_advanced' => '1',
 *              'simple_action' => 'by_percent',
 *              'discount_amount' => '10',
 *              'coupon_type' => '2',
 *              'store_ids' =>
 *              [
 *                  0 => '42',
 *                  1 => '41',
 *                  2 => '36',
 *                  3 => '34',
 *                  4 => '35',
 *                  5 => '31',
 *                  6 => '37',
 *              ],
 *              'currencies' =>
 *              [
 *                  0 => 'BRL',
 *                  1 => 'CHF',
 *                  2 => 'EUR',
 *              ],
 *              'customer_group_ids' =>
 *              [
 *                  0 => '0',
 *                  1 => '1',
 *                  2 => '2',
 *                  3 => '3',
 *                  4 => '4',
 *              ],
 *              'website_ids' =>
 *              [
 *                  0 => '11',
 *              ],
 *              'coupon_code' => time(),
 *          ],
 *      ],
 *  ]
 *
 * Full Example
 * [
 *      'action' => 'salesrule/create',
 *      'parameters' =>
 *      [
 *          'data' =>
 *          [
 *              'name' => 'Marcin',
 *              'description' => '',
 *              'from_date' => '',
 *              'to_date' => '',
 *              'uses_per_customer' => '0',
 *              'is_active' => '1',
 *              'stop_rules_processing' => '1',
 *              'is_advanced' => '1',
 *              'product_ids' => '',
 *              'sort_order' => '0',
 *              'simple_action' => 'by_percent',
 *              'discount_amount' => '10',
 *              'discount_qty' => '0',
 *              'discount_step' => '0',
 *              'simple_free_shipping' => '0',
 *              'apply_to_shipping' => '0',
 *              'times_used' => '0',
 *              'is_rss' => '0',
 *              'coupon_type' => '2',
 *              'use_auto_generation' => 0,
 *              'uses_per_coupon' => '',
 *              'promo_sku' => '',
 *              'promo_cats' => '',
 *              'store_ids' =>
 *              [
 *                  0 => 'store:code/de_de#id',
 *                  1 => 'store:code/at_de#id',
 *              ],
 *              'currencies' =>
 *              [
 *                  0 => 'BRL',
 *                  1 => 'CHF',
 *                  2 => 'EUR',
 *              ],
 *              'customer_group_ids' =>
 *              [
 *                  0 => 'customer_group:customer_group_code/Retailer#id',
 *                  1 => 'customer_group:customer_group_code/Business#id',
 *              ],
 *              'website_ids' =>
 *              [
 *                  0 => 'website:code/eset#id',
 *              ],
 *              'coupon_code' => '1425469200',
 *              'compound_discount_amounts' =>
 *              [
 *                  0 =>
 *                  [
 *                      'rate' =>
 *                      [
 *                          'BRL' => '1',
 *                          'CHF' => '1',
 *                          'EUR' => '1.000000000000',
 *                      ],
 *                      'currency' => 'CHF',
 *                      'default' => '10.00',
 *                      'use_default' => '1',
 *                  ],
 *                  1 =>
 *                  [
 *                      'rate' =>
 *                      [
 *                          'BRL' => '1',
 *                          'CHF' => '1',
 *                          'EUR' => '1',
 *                      ],
 *                      'currency' => 'EUR',
 *                      'default' => '10.00',
 *                      'use_default' => '1',
 *                  ],
 *              ],
 *              'store_labels' =>
 *              [
 *                  0 => '',
 *                  42 => '',
 *                  41 => '',
 *                  36 => '',
 *                  34 => '',
 *                  35 => '',
 *                  31 => '',
 *                  37 => '',
 *              ],
 *              'import_clear' => '0',
 *              'generate_num' => '',
 *              'generate_pattern' => '',
 *          ]
 *          ,
 *      ],
 *  ]
 *
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Marcin Rogacki <mrogacki@nexway.com>
 * @copyright   Copyright (c) 2015, Nexway
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     */
    protected function _create()
    {
        $this->getParameters()->setModel('salesrule/rule');

        return parent::_create();
    }
}
