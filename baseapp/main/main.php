<?php
$dbDatas = \common\helpers\InitFormat::formatDb();
return [
    'timeZone'=>'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
	'bootstrap' => require(__DIR__ . '/main-bootstrap.php'),
	'controllerMap'=> \common\helpers\InitFormat::runtimeParams('controller-front'),

	'components' => Yii\helpers\ArrayHelper::merge(
		require(__DIR__ . '/main-common.php'), 
		require(__DIR__ . '/main-url-manager.php'),
		$dbDatas
	),
];
