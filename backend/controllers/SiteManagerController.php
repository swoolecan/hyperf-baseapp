<?php
namespace backend\controllers;

use Yii;

class SiteManagerController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDoclist()
    {
        return $this->render('document/doclist');
    }

    public function actionDocview($code = null)
    {
        $this->layout = '@backend/views/charisma/site-manager/document/layout';
        echo $this->render("document/{$code}");
        exit();
    }

	public function actionVuepackage()
	{
		$basePath = $this->getInputParams('base', 'postget');
		$basePath = empty($basePath) ? 'app-culture' : $basePath;
		$file = "/data/htmlwww/frontend/{$basePath}/package.json";
		$str = file_get_contents($file);
		$datas = json_decode($str, true);
		$submit = $this->getInputParams('submit', 'postget');
		if ($submit) {
			return $this->writePackageFile($datas, $file);
		}

		$cache = Yii::$app->cache; 
		$cacheKey = 'npm-module-versions';
		$caches = $cache->get($cacheKey);

		$extPath = $this->getInputParams('ext', 'postget');
		$extPath = empty($extPath) ? '' : $extPath;
		$fileExt = "/data/htmlwww/frontend/{$extPath}/package.json";
		$strExt = @ file_get_contents($fileExt);
		$datasExt = json_decode($strExt, true);
		$forceVersion = $this->getInputParams('force_version', 'postget');
		$result = $this->formatData($datas['dependencies'], $caches, $forceVersion, '');
		$result = $this->formatData($datas['devDependencies'], $caches, $forceVersion, '-dev');
		if (!empty($datasExt)) {
		    $result = $this->formatData($datasExt['dependencies'], $caches, $forceVersion, '-ext');
		    $result = $this->formatData($datasExt['devDependencies'], $caches, $forceVersion, '-extdev');
		}
		$lastVersion = $result['lastVersion'];
		unset($result['lastVersion']);
		$lastVersion = array_merge($caches, $lastVersion);
		$cache->set($cacheKey, $lastVersion);
		ksort($result);

        return $this->render('vuepackage', ['datas' => $result, 'basePath' => $basePath, 'extPath' => $extPath]);
	}

	protected function formatData($datas, $caches, $forceVersion, $versionSuffix = '')
	{
		static $result = ['lastVersion' => []];
		foreach ($datas as $key => $version) {
			if (isset($result[$key])) {
				$result[$key]['version' . $versionSuffix] = $version;
				$result['lastVersion'][$key] = $result[$key]['version_last'];
				continue;
			}
			$versionLast = !$forceVersion && isset($caches[$key]) ? $caches[$key] : exec('sudo npm view ' . $key . ' version', $result);
			$result[$key] = [
				'code' => $key,
				'version' . $versionSuffix => $version,
				'version_last' => $versionLast,
			];
			$result['lastVersion'][$key] = $versionLast;
	    }
		return $result;
	}

	public function actionFrontvue()
	{
		$files = [
			'mobile' => '/tmp/ydd-mobile-build.txt',
			'shop' => '/tmp/ydd-shop-build.txt',
			'admin' => '/tmp/ydd-admin-build.txt',
		];
		$updateApp = $this->getInputParams('update_app');
		$viewApp = $this->getInputParams('view_app');
		if (!empty($viewApp)) {
			$content = file_get_contents($files[$viewApp]);
			echo $content;exit();
		}
		if (empty($updateApp)) {
		    return $this->render('frontvue');
		}

		$dir = '/data/htmlwww/frontend/';
		$app = $updateApp;
		$commands = [
			'admin' => "cd {$dir}ydd-{$app} && sudo /usr/local/bin/git pull origin master 2>&1 && sudo /usr/local/bin/npm run build > {$files[$app]} && sudo /usr/local/bin/git log >> {$files[$app]}",
			'mobile' => "cd {$dir}ydd-{$app} && sudo /usr/local/bin/git pull origin master 2>&1 && sudo /usr/local/bin/npm run build > {$files[$app]} && sudo /usr/local/bin/git log >> {$files[$app]}",
			'shop' => "cd {$dir}ydd-{$app} && sudo /usr/local/bin/git pull origin master 2>&1 && sudo /usr/local/bin/npm run build > {$files[$app]} && sudo /usr/local/bin/git log >> {$files[$app]}",
		];
		$command = $commands[$updateApp];
        exec($command, $result, $rStatus);

		$url = "?view_app={$app}";
		header("Location: {$url}");
	}

	public function writePackageFile($datas, $file)
	{
		$dependencies = $this->getInputPosts('dependencies');
		$devDependencies = $this->getInputPosts('devDependencies');
		$infos = $infosExt = [];
		foreach ($dependencies as $key => $version) {
			$version = trim($version);
			if (empty($version)) {
				continue;
			}
			$infos[$key] = $version;
		}
		foreach ($devDependencies as $key => $version) {
			$version = trim($version);
			if (empty($version)) {
				continue;
			}
			$infosExt[$key] = $version;
		}
		ksort($infos);
		ksort($infosExt);
		$datas['dependencies'] = $infos;
		$datas['devDependencies'] = $infosExt;
		$jsonStr = json_encode($datas, JSON_PRETTY_PRINT);
		$jsonStr = str_replace('    ', '  ', $jsonStr);
		$jsonStr = str_replace('\/', '/', $jsonStr);
		file_put_contents($file, $jsonStr);
		exit();
	}
}
