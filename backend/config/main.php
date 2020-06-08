<?php
return yii\helpers\ArrayHelper::merge([
//$return = yii\helpers\ArrayHelper::merge([
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'viewPath' => '@backend/views/charisma',
    'controllerNamespace' => 'backend\controllers',
	//'controllerMap'=> \common\helpers\InitFormat::runtimeParams('controller-backend'),
    'components' => [
        'user' => [
            'identityClass' => 'backend\models\Manager',
            'enableAutoLogin' => true,
			'loginUrl' => '/signin.html',
			'idParam' => '_backend',
			'identityCookie' => ['name' => '_backend_identity', 'httpOnly' => true],
        ],
        'session' => [           
            'cookieParams' => ['domain' => '.' . Yii::getAlias('@domain-base'), 'lifetime' => 24*3600*30],            
            'timeout' => 24*3600*30,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
		'urlManager' => [
			'rules' => \common\helpers\RuleFormat::pointRules('backend'),
		],
    ],

    'as access' => [
        'class' => 'backend\components\AccessControl',
        'allowActions' => [
            'entrance/*',
            'site/error',
			'upload/*',
            'debug/*',
        ]
    ],

    'params' => \common\helpers\InitFormat::formatAppParams('backend'),
], \common\helpers\InitFormat::formatMain('backend'));
print_r($return);exit();
