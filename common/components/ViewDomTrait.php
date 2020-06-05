<?php

namespace common\components;

use Yii;

trait ViewDomTrait
{
	public function echoDomFiles($type, $files, $viewSort = null)
	{
		$files = $this->formatDomFiles($files, $viewSort);
		$str = '';
		foreach ($files as $file) {
		    $str .= $this->getDomStr($type, $file);
		}
		return $str;
	}

	public function formatDomFiles($files, $viewSort = null)
	{
		$assetSort = 'asseturl';
		$datas = [];
        foreach ((array) $files as $file) { 
			if (strpos($file, '@') !== false) {
				list($assetSort, $file) = explode($file, '@');
			}
			if (substr($file, 0, 1) === '/') {
				$url = $this->formatFileUrl($file, '', '', $assetSort);
			} else {
				$url = $this->formatFileUrl($file, $viewSort, $assetSort);
			}
			$datas[] = $url;
		}
		return $datas;
	}

    public function getDomStr($type, $file, $options = [])
	{
		$str = $oStr = '';
		foreach ($options as $attr => $value) {
			$oStr .= "{$attr}='{$value}' ";
		}

		switch ($type) {
		case 'link':
            $str = "<link rel='stylesheet' type='text/css' href='{$file}.css' {$oStr}/>";
			break;
		case 'js':
			$verStr = '?v=' . time();
		    $str = "<script type='text/javascript' src='{$file}.js{$verStr}' {$oStr}></script>";
			break;
		case 'img':
            $str = "<img src='{$file}?v=1' {$oStr} />";
			break;
		}
		return $str;
	}

	public function formatFileUrl($baseUrl, $viewSort = null, $assetSort = null)
	{
        $assetSort = isset($assetSort) ? $assetSort : 'asseturl';
		$viewSort = $this->_getViewSort($viewSort);

		$path = $this->_viewSortDatas($viewSort);
		$file = '/' . $path . $this->_viewBaseUrl($viewSort, $baseUrl);
		$file = str_replace('//', '/', $file);
		$file = str_replace('//', '/', $file);
		return Yii::getAlias("@{$assetSort}") . "{$file}";
	}

	protected function _viewSortDatas($viewSort = null)
	{
		$datas = [
			'fshop' => [
				'name' => '花加模板',
			],
			'wshop' => [
				'name' => '人人商城模板',
			],
			'wpshop' => [
				'name' => 'wordpress商城',
			],
			'wxwall' => [
				'name' => '微信上墙',
				'path' => 'spage/wxwall/',
			],
			'reself' => [
				'name' => '投放系统',
				'path' => 'resource/custom/',
			],
			'respread' => [
				'name' => '投放系统',
				'path' => 'resource/spread/',
			],
		];
		if (is_null($viewSort) || !isset($datas[$viewSort])) {
			return $datas;
		}

		$path = isset($datas[$viewSort]['path']) ? $datas[$viewSort]['path'] : $viewSort . '/asset/';
		return $path;
	}

	public function _getViewSort($viewSort)
	{
        $viewSort = is_null($viewSort) ? (isset($this->params['viewSort']) ? $this->params['viewSort'] : '') : $viewSort;
		if (!empty($viewSort)) {
			return $viewSort;
		}
		$vDatas = $this->_viewSortDatas();
		foreach ($vDatas as $vSort => $value) {
			if (strpos($this->viewFile, $vSort) !== false) {
				return $vSort;
			}
		}
		return '';
	}

	protected function _viewBaseUrl($viewSort, $baseUrl)
	{
		$datas = require(Yii::getAlias('@views/config/params-point.php'));
		if (!isset($datas[$viewSort])) {
			return $baseUrl;
		}

		if (!in_array($baseUrl, $datas[$viewSort])) {
			return $baseUrl;
		}

		$base = basename($baseUrl);
		$baseUrl = str_replace($base, $this->getSiteElem('code') . '-' . $base, $baseUrl);
		return $baseUrl;
	}
}
