<?php
return [
    'urlManager' => [
        'class' => 'yii\web\UrlManager',
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'suffix' => '.html',
        'rules' => [
            ['pattern' => '/captcha', 'route' => '/site/captcha'],
            ['pattern' => '/qrcode', 'route' => '/site/qrcode'],
            ['pattern' => '/wechat-only', 'route' => '/site/wechat-only'],
            ['pattern' => '/spread-record', 'route' => '/site/spread-record'],
            //['pattern' => '/upload/<action:(show|download)>', 'route' => '/upload/<action>'],
            //['pattern' => '/upload/<table>/<field>', 'route' => '/upload/index'],
            'debug/<controller>/<action>' => 'debug/<controller>/<action>',
        ],
    ]
];
