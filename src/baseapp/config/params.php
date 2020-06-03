<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'corOrigins' => [Yii::getAlias('@backendurl')],
    'currentTime' => time(),

	'corOrigins' => [
	],

	'globalDatas' => [],//'merchant', 'website', 'wechat'],
    'currentWechat' => '',
    'currentWechatOpen' => '',
    'currentMerchant' => '',
    'currentWebsite' => '',

	'access-token-test' => '',
	'access-token-testclient' => '',

    'pathParams' => ['default' => '/data/htmlwww/filesys/path', 'website' => '/data/htmlwww/path2'],  
    'urlParams' => ['default' => 'http://upload.domain.com', 'website' => 'http://upload.domain2.com'],  

    'luosimaoCaptcha' => ['siteKey' => ''],  
	'noSendMessage' => true,
	'pointModelDatas' => [],

	'errorNotice' => [
		'noNotice' => true,
        'app_id' => '',
		'secret' => '',
		'touser' => [],
		'ignore' => [
			'yii\web\NotFoundHttpException',
			'yii\web\HeadersAlreadySentException',
			'yii\web\HttpException',
		],
        'template_id' => 'Zvk-q9gdhaeYPNTgkLRQ7EE0ptkoPgyGww8UPQLDl_g',
	],
];
