salesrule/create
================

```php
[
    [
        'action' => 'salesrule/create',
        'parameters' => [
            'data' => [
                'name' => 'Marcin',
                'description' => '',
                'from_date' => '',
                'to_date' => '',
                'uses_per_customer' => '0',
                'is_active' => '1',
                'stop_rules_processing' => '1',
                'is_advanced' => '1',
                'product_ids' => '',
                'sort_order' => '0',
                'simple_action' => 'by_percent',
                'discount_amount' => '10',
                'discount_qty' => '0',
                'discount_step' => '0',
                'simple_free_shipping' => '0',
                'apply_to_shipping' => '0',
                'times_used' => '0',
                'is_rss' => '0',
                'coupon_type' => '2',
                'use_auto_generation' => 0,
                'uses_per_coupon' => '',
                'promo_sku' => '',
                'promo_cats' => '',
                'store_ids' => [
                    0 => 'store:code/de_de#id',
                    1 => 'store:code/at_de#id',
                ],
                'currencies' => [
                    0 => 'BRL',
                    1 => 'CHF',
                    2 => 'EUR',
                ],
                'customer_group_ids' => [
                    0 => 'customer_group:customer_group_code/Retailer#id',
                    1 => 'customer_group:customer_group_code/Business#id',
                ],
                'website_ids' => [
                    0 => 'website:code/eset#id',
                ],
                'coupon_code' => '1425469200',
                'compound_discount_amounts' => [
                    0 => [
                        'rate' => [
                            'BRL' => '1',
                            'CHF' => '1',
                            'EUR' => '1.000000000000',
                        ],
                        'currency' => 'CHF',
                        'default' => '10.00',
                        'use_default' => '0',
                        'amount' => '20.00',
                    ],
                    1 => [
                        'rate' => [
                            'BRL' => '1',
                            'CHF' => '1',
                            'EUR' => '1',
                        ],
                        'currency' => 'EUR',
                        'default' => '10.00',
                        'use_default' => '0',
                        'amount' => '30.00',
                    ],
                ],
                'import_clear' => '0',
                'generate_num' => '',
                'generate_pattern' => '',
            ],
        ],
    ]
];
```

Fields
------

### What you should know:

```php
'name' => 'Marcin',
```

A name of salerule which is displayed on grid list in admin.

```php
'simple_action' => 'by_percent',
```

Type of sale rule:
* `by_percent` - counts percent of target items and make discount of that percent
* `cart_fixed` - take an fixed value and substract it from total amount e.g. fixed value = 5, items = 3, total = 5
* `by_fixed` - take an fixed value and substract it from each item price e.g. fixed value = 5, items = 3, total = 15

```php
'discount_amount' => '10',
```

An amount of discount. Eg. 10 percent when `simple_action` is set to `by_percent`, 10 EUR when `simple_action` is set to `cart_fixed` or `by_fixed`.

```php
'coupon_type' => '2',
```

With or without coupon. A value 1 = no, 2 = yes.

```php
'store_ids' => []
```

Stores where discoint will be available. Supports extended ids.

```php
'currencies' => []
```

Currencies on which discount can be applied.

```php
'customer_group_ids' => []
```

Customer types on which discount can be applied. Supports extended ids.

```php
'website_ids' => []
```

Websites where discoint will be available. Supports extended ids.

```php
'coupon_code' => '1425469200',
```

A coupon code - those which need to be applied on the cart interfece by customer.

```php
'compound_discount_amounts' =>

[
    0 =>
    [
        'rate' =>
        [
            'BRL' => '1',
            'CHF' => '1',
            'EUR' => '1.000000000000',
        ],
        'currency' => 'CHF',
        'default' => '10.00',
        'use_default' => '0',
        'amount' => '20.00',
    ],
    1 =>
    [
        'rate' =>
        [
            'BRL' => '1',
            'CHF' => '1',
            'EUR' => '1',
        ],
        'currency' => 'EUR',
        'default' => '10.00',
        'use_default' => '1',
        'amount' => '40.00',
    ],
],
```

Available only when `simple_action` is set to `cart_fixed` or `by_fixed`.
Allows define different values for each currency. When flag `use_default` is
set to `0` then discount amount is taken from `amount` otherwise (value `1`)
discount amount is taken from `default`.
