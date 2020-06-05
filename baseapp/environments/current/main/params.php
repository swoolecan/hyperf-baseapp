<?php
return [
	'corOrigins' => [
        Yii::getAlias('@backendurl'),
        Yii::getAlias('@restappurl'),
	],

	'globalDatas' => ['merchant', 'website', 'wechat'],
    'currentWechat' => 'ydd',//bbndirect',
    'currentWechatOpen' => 'bbnopen',
    'currentMerchant' => '2',
    'currentWebsite' => 'bbn',

    'pathParams' => [
		'base' => '/data/htmlwww/filesys/salesys',
        'shop' => '/data/htmlwww/filesys/salesys',
    ],
    'urlParams' => [
        'default' => 'http://salesys.up.beeboone.net',
        'website' => 'http://upload.alyee.com',
    ],  
    'luosimaoCaptcha' => [
        'siteKey' => '',
    ],  

    'noWechatMessage' => true,
    'errorNotice' => [
        'noNotice' => true,
        'app_id' => 'wxb2b4a0826895e79b',
        'secret' => '',
        'touser' => [
            '',
        ],
        'ignore' => [
            'yii\web\NotFoundHttpException',
			'yii\web\HeadersAlreadySentException',
			'yii\web\HttpException',
        ],
        'template_id' => 'Gjaq0zI3m-8LaKTZC6kQchgY30ApQhVGl4Q_CFz87ts',
    ],
];
