salesrule/create
================

```php
[
    [
        'action' => 'upsell/create',
        'parameters' => [
            'data' => [
                'title' => 'Totem-upsell-01',
                'stores' => [
                    'store:code/de_de#id',
                ],
                'asset_target_product_name' => [],
                'asset_target_product_short_description' => [],
                'start_date' => '',
                'end_date' => '',
                'priority' => '',
                'status' => 'active',
                'allow_recursive' => 0,
                'display_location' => 3, //interstitial (add doc)
                'interstitial_page_id' => 'interstitial_page:name/Totem%20interstitial%20page%20one#id',
                'show_countdown' => 0,
                'countdown_ttl' => 30,
                'type' => 'configuration',
                'source_product_id' => 'product:sku/ESETCS13AD11999#id',
                'target_product_id' => 'product:sku/ESETCS13AD11999#id',
                'all_custom_options' => '0',
                'source_product' => [
                    '360d-1pc',
                ],
                'target_product' => [
                    '360d-2pc',
                ]
            ],
        ],
    ],
];
```

Fields
------

### What you should know:

```php
'title' => 'Upsell title',
```

A name of upsell which is displayed on grid list in admin.

```php
'stores' => '['store:code/de_de#id', 'store:code/ch_fr#id']
```

Array with stores extended ids separated by comma

```php
'asset_target_product_name' => [
    'de_DE' => 'Target name for de_DE locale',
    'ch_FR' => 'Target name for ch_FR locale',
]
```

Target name displayed before add upsell to cart

```php
'asset_target_product_short_description' => [
    'de_DE' => 'Target description for de_DE locale',
    'ch_FR' => 'Target description for ch_FR locale',
]
```

Target description displayed before add upsell to cart


```php
'priority' => '2',
```

Upsell priority, integer value or empty for autocomplete

```php
'status' => 'active'
```

Status active|inactive, to enable/disable upsell.

```php
'allow_recursive' => 1
```

Allow/disallow recursive upsell. Values: 0/1

```php
'display_location' => 3
```

Location of upsell
3 - Interstitial page
2 - By product (in checkout below source product)
1 - Bottom (bottom of checkout reviwe block)

```php
'interstitial_page_id' => 'interstitial_page:name/Totem%20interstitial%20page%20one#id'
```

Interstitial page extended id (only if display_location is set to 3). Use url encode on name.

```php
'source_product_id' => 'product:sku/ESETCS13AD11999#id',
```

Source product extended id

```php
'target_product_id' => 'product:sku/ESETCS13AD11999#id',
```

Target product extended id


```php
'all_custom_options' => 0,
```

Upsell contains all custo options of product or not. If value is set to 1 'source_product' and 'target_product' data should be empty.


```php
'source_product' => '360d-1pc',
```

Options titles of source product


```php
'target_product' => '360d-2pc',
```

Options titles of target product