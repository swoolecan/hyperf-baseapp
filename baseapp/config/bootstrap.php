<?php
$baseDir = dirname(dirname(__DIR__));
$bootstrapFile = "{$baseDir}/baseapp/environments/current/global/alias.php";
$bootstraps = file_exists($bootstrapFile) ? require($bootstrapFile) : [];
$aliasKeys = array_merge([
	'common', 
	'console', 
	'backend', 
	'baseapp',
	'views',
	'assetcustom',

    'domain-base',
	
	'asset',
	'staticurl',
	'asseturl', 
	'backendurl', 
	'defaulturl',
], $bootstraps);
$aValues = require(__DIR__ . '/bootstrap-local.php');

foreach ($aliasKeys as $aKey) {
	$aValue = isset($aValues[$aKey]) ? $aValues[$aKey] : "{$baseDir}/{$aKey}";
	Yii::setAlias($aKey, $aValue);
}
unset($baseDir);
unset($bootstrapFile);
unset($bootstraps);
unset($aliasKeys);
unset($aValues);
unset($aKey);
unset($aValue);
