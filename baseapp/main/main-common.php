<?php
return [
    'cache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@backend/runtime/cache1',
    ],
    'assetManager' => [
		'basePath' => '@assetcustom/assets',
        'baseUrl' => '@staticurl/assets',
    ],
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],
    'request' => [
        'class' => 'common\components\Request',
		'enableCsrfCookie' => false,
    ],
    'view' => [
        'class' => 'common\components\View',
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mail',
        'useFileTransport' => false,
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => '', // 自己申请邮箱时的服务器
            'username' => '', // 申请时的账号
            'password' => '', // 申请时的密码
            'port' => '25', // 端口
            'encryption' => 'tls', // 一种加密方式，报错时删除它再试试（我这里就不支持这种加密方式）
        ],
        'messageConfig' => [
            'charset' => 'UTF-8', // 设置字符集
            //'from' => ['？？？' => ？？？] // 给发送的邮箱起个别名
        ],
    ],
];
