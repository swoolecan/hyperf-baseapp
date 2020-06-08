<?php

namespace backend\models;

use Yii;

trait MenuTrait
{
	public function settingSort($sort, $ids)
	{
		$sort = $sort == 'backend' ? '' : $sort;
		$ids = explode(',', $ids);
		$ids = array_filter($ids);
		$infos = $this->getInfos(['where' => ['id' => $ids]]);
		foreach ($infos as $info) {
			if ($info->module == '' && $sort == 'merchant') {
				continue;
			}
			$info->sort = $sort;
			$r = $info->update(false, ['sort']);
			var_dump($r);
		}
		return true;
	}

	public function getControllerMethods()
	{
		$menus = $this->getInfos(['limit' => 1000]);
		$datas = [];
		foreach ($menus as $menu) {
			if (!empty($menu['controller'])) {
				//$key = $menu->createCode(true);
			    $datas[$menu['elem_code']][$menu['method']] = $menu;
			}
		}
		return $datas;
	}

	public function createCode($getControllerKey = false, $data = null)
	{
		$module = is_null($data) ? $this->module : $data['module'];
		$method = is_null($data) ? $this->method : $data['method'];
		$controller = is_null($data) ? $this->controller : $data['controller'];
		$str = str_replace('/', '-', $module) . '_' . $controller;
		if ($getControllerKey) {
			return $str;
		}
		return $str . '-' . $method;
	}

	public function _addInfo($data)
	{
		if (!in_array($data['module'], array_keys($this->moduleInfos))) {
			return ;
		}
		$exist = $this->getInfo(['where' => ['module' => $data['module'], 'controller' => $data['controller'], 'method' => $data['method']]]);
		if ($exist) {
			return true;
		}

		$data['sort'] = $data['sort'] == 'backend' ? '' : ($data['sort'] == 'merchant' ? 'merchant' : 'merback');
		$defaultDatas = [
			'listinfo' => ['display' => 2],
			'add' => ['display' => 3],
			'add-mul' => ['display' => 3],
			'update' => ['display' => 4],
			'delete' => ['display' => 4],
		];
		$defaultData = isset($defaultDatas[$data['method']]) ? $defaultDatas[$data['method']] : [];
		$data['display'] = isset($defaultData['display']) ? $defaultData['display'] : 99;
		return $this->addInfo($data);
	}

	public function createUrl($baseUrl = null)
	{
		$baseUrl = is_null($baseUrl) ? Yii::getAlias('@backendurl') : $baseUrl;
		if (empty($this->controller)) {
			return $baseUrl;
		}
		$module = $this->module;
		$module = $module == 'backend' ? '' : $module;
		if (strpos($module, '/') !== false) {
			$module = substr($module, 0, strpos($module, '/'));
		}

        $url = "/{$module}/{$this->controller}/{$this->method}.html";
        if (!empty($this->extparam)) {
            $url .= '?' . $this->extparam;
        }
        $url = str_replace('//', '/', $url);
		return trim($baseUrl, '/') . $url;
	}

	public function checkSort($update)
	{
		$infos = $this->getInfos();
		foreach ($infos as $info) {
			$parentCode = $info['parent_code'];
			if (empty($parentCode)) {
				continue;
			}
			$parentInfo = $info->parentInfo;
			if ($parentInfo['sort'] == 'merback' || $parentInfo['sort'] == $info['sort']) {
				continue;
			}

			if ($update) {
				$parentInfo['sort'] = 'merback';
				$parentInfo->update(false, ['sort']);
			}
			echo "error sort-{$info['code']}-{$info['name']}-{$info['sort']}=={$parentInfo['code']}-{$parentInfo['name']}-{$parentInfo['sort']}==<br />";
		}
	}

	public function getDemo()
	{
		if (in_array($this->method, ['update', 'view', 'delete', 'callback'])) {
    		$demoId = 1;
    		$elem = $this->getPointModel('base-elem')->getInfo($this->elem_code, 'name');
    		if (!empty($elem) && empty($elem->no_model)) {
                $model = $this->getPointModel($elem->name);
    		    $demo = empty($model) ? false : $model->getInfo([]);
    			$demoId = empty($demo) ? 1 : $demo['id'];
    		}
			$url = $this->appendUrlParams($url, ['id' => $demoId]);
		}
		
		$urlAjax = $url . (strpos($url, '?') !== false ? '&force_ajax=1' : '?force_ajax=1');
		$urlJson = $url . (strpos($url, '?') !== false ? '&force_ajax=1&return_json=1' : '?force_ajax=1&return_json=1');

		return "--<a href='{$urlAjax}' target='_blank'>ajax</a>==<a href='{$urlJson}' target='_blank'>json</a>";
	}
}
