<?php
return [
    'mall' => [
        'logo' => [],
    ],
    'article' => [
        'thumb' => [],
    ],
    'friendlink' => [
        'logo' => ['isSingle' => true, 'minSize' => 1, 'maxSize' => 300, 'type' => 'image/*', ],
    ],
    'company' => [
        'logo' => ['isSingle' => true, 'minSize' => 1, 'maxSize' => 300, 'type' => 'image/*', ],
    ],
    'attachment' => [
        'import' => ['isSingle' => true, 'minSize' => 0.1, 'maxSize' => 30000, 'type' => ['application/*', 'text/csv'], ],
    ],
    'scene' => [
        'picture' => [''],
        'picture_wechat' => [],
    ],
    'import_record' => [
        'import' => ['minSize' => 0.1, 'maxSize' => 30000, 'type' => ['application/*', 'text/csv']],
    ],
    'business_express' => [
        'import' => ['minSize' => 0.1, 'maxSize' => 30000, 'type' => ['application/*', 'text/csv']],
    ],
    'website_goods' => [
        'picture' => ['isSingle' => false],
        'slide' => ['isSingle' => false],
    ],
    'website_sku' => [
        'slide' => ['isSingle' => false],
    ],
    'goods' => [
        'picture' => ['isSingle' => false],
        'slide' => ['isSingle' => false],
    ],
    'goods_sku' => [
        'slide' => ['isSingle' => false],
    ],
    'info' => [
        'picture' => ['isSingle' => false, 'maxSize' => 800],
    ],
    'brand' => [
        'logo' => [],
    ],
    'position' => [
        'picture' => [],
        'picture_mobile' => [],
    ],
    'website' => [
        'picture' => [],
        'picture_brand' => [],
        'picture_figure' => [],
        'logo' => [],
        'wechat_picture' => [],
        'share_picture' => [],
    ],
    //third
    'material' => [
        'image' => ['isSingle' => false, 'minSize' => 1, 'maxSize' => 2000, 'type' => 'image/*'],
        'thumb' => ['isSingle' => false, 'minSize' => 1, 'maxSize' => 64, 'type' => 'image/*'],
        'voice' => ['isSingle' => false, 'minSize' => 1, 'maxSize' => 2000, 'type' => '*'],
        'vedio' => ['isSingle' => false, 'minSize' => 1, 'maxSize' => 10000, 'type' => '*'],
    ],
    // spread
    'planfee' => [
        'import' => ['minSize' => 0.1, 'maxSize' => 30000, 'type' => ['application/*', 'text/csv']],
    ],
    'import_keywordfee' => [
        'import' => ['minSize' => 0.1, 'maxSize' => 30000, 'type' => ['application/*', 'text/csv']],
    ],
    // paytrade
    'activity' => [
        'picture' => [],
    ],
    'coupon_sort' => [
        'thumb' => [],
    ],
    //merchant
    'contract' => [
        'document' => ['isSingle' => true, 'minSize' => 0.01, 'maxSize' => 30000, 'type' => ['application/*', 'text/csv']],
        'picture' => ['isSingle' => false, 'minSize' => 1, 'maxSize' => 2000, 'type' => 'image/*'],
    ],
    'datum' => [
        'picture' => ['isSingle' => false, 'minSize' => 1, 'maxSize' => 2000, 'type' => 'image/*'],
    ],
];
