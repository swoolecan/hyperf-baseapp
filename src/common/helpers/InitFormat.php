<?php

namespace common\helpers;

use Yii;

class InitFormat
{
    public static function formatDb()
    {
		$dbDatas = self::environmentParams('global','db');
		$locals = self::getBaseParams('config/database-local.php');
		$dbLocals = isset($locals['db']) ? $locals['db'] : [];
		$account = isset($locals['account']) ? $locals['account'] : [];
		$return = [];
        foreach ($dbDatas as $database => $dataInfo) {
			$dataInfo = array_merge($dataInfo, $account);
			$dataInfo = isset($dbLocals[$database]) ? array_merge($dataInfo, $dbLocals[$database]) : $dataInfo;
			$prefix = isset($dataInfo['prefix']) ? $dataInfo['prefix'] : 'wp_';
            $return[$database] = [ 
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=' . $dataInfo['host'] . ';dbname=' . $dataInfo['name'],
                'username' => $dataInfo['dbUser'],
                'password' => $dataInfo['dbPassword'],
				'charset' => 'utf8mb4',
                'tablePrefix' => $prefix,
            ];  
        }
		return $return;
	}

    public static function formatAppParams($code)
    {
		$params = self::getBaseParams('config/params.php');
        $paramsApp = self::getBaseParams('config/prams.php', false, $code);
        $paramsCurrent = self::getBaseParams('environments/current/main/params.php', false);
        $paramsLocal = self::getBaseParams('config/params-local.php');
        $paramsAppLocal = self::getBaseParams('config/params-local.php', false, $code);

        $result = array_merge($params, $paramsApp, $paramsCurrent, $paramsLocal, $paramsAppLocal);
        //print_R($result);exit();
        return $result;
	}

    public static function formatMain($code)
    {
        $mainCurrent = self::getBaseParams("environments/current/main/{$code}.php", false);
        $mainLocal = self::getBaseParams('main/main-local.php', false);
        $mainApp = self::getBaseParams('config/main-local.php', false, $code);
        $return = \yii\helpers\ArrayHelper::merge($mainCurrent, $mainLocal, $mainApp);
        return $return;
    }

	public static function environmentParams($sort, $key)
	{
		static $datas;
		if (isset($datas[$sort]) && isset($datas[$sort][$key])) {
			return $datas[$sort][$key];
		}
		if (!isset($datas[$sort])) {
			$datas[$sort] = [];
		}

		$currentFile = "environments/current/{$sort}/{$key}.php";
		$currentParams = self::getBaseParams($currentFile, false);
		$file = "environments/{$sort}/{$key}.php";
		$params = self::getBaseParams($file);
		$datas[$sort][$key] = array_merge($params, $currentParams);
		return $datas[$sort][$key];
	}

	public static function runtimeParams($code)
	{
		$file = "runtime/map-{$code}.php";
		$params = static::getBaseParams($file, false);
		if (in_array($code, ['model'])) {
			$baseParams = static::getBaseParams('config/map-' . $code . '.php', false, 'backend');
			$params = array_merge($baseParams, $params);
		}
		return $params;
	}

	public static function ruleDatas($app, $modules = [])
	{
        $rules = static::getBaseParams('config/rule.php', true, $app);
        if (!empty($modules)) {
			foreach ($modules as $module) {
                $rulesModule = static::getBaseParams("{$module}/config/rule.php", true, $app);
                $rules = array_merge($rules, $rulesModule);
			}
        }
		return $rules;
	}

	/*public static function siteDatas($app, $modules = [])
	{
        $sites = static::getBaseParams('config/site.php', false, $app);
        $currentSites = static::getBaseParams("environments/current/site/{$app}.php", false);
		return array_merge($sites, $currentSites);
    }*/

    public static function getBaseParams($file, $throwError = true, $alias = 'baseapp')
    {
        $file = Yii::getAlias("@{$alias}/{$file}");
		if (!file_exists($file) && $throwError) {
			require($file);
		}
        //echo $file;
        return file_exists($file) ? require($file) : [];
    }
}
