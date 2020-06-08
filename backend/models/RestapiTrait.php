<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

trait RestapiTrait
{
	public function getMenuDatas($mCode, $type = null)
	{
		$params = [
			'where' => ['menu_code' => $mCode, 'sort' => $type],
			'orderBy' => ['serial' => SORT_DESC],
			'select' => 'id,name,menu_code,method,sort',
		];

		$datas = $this->getInfos($params);
		return $datas;
	}

	public function getVisitUrl($onlyUrl = false)
	{
		$url = Yii::getAlias('@backendurl') . '/rest-client/request/create.html?id=' . $this->id;
		if ($onlyUrl) {
			return $rul;
		}
		$name = $this->name . '-' . $this->serial;
		return "<a href='{$url}' target='_blank'>{$name}</a>";
	}

	public function formatDatas(& $requestForm, & $record) 
	{
        $request = unserialize($this->request_lock);
		//print_r($request);exit();
        $response = unserialize($this->response_lock);

		$requestForm->method = $this->method;
		$requestForm->endpoint = $this->endpoint;
		$requestForm->description = $this->description;
        $requestForm->setAttributes($request);

        $record->status = $response['status'];
        $record->duration = $response['duration'];
        $record->headers = $response['headers'];
        $record->content = $response['content'];
		return ;
	}

	public function updateApi($requestForm, $record)
	{
		//print_R($requestForm);exit();
		$this->request = serialize($requestForm->getAttributes());
		$this->response = serialize(get_object_vars($record));
		$this->method = $requestForm->method;
		$this->endpoint = $requestForm->endpoint;
		$this->description = $requestForm->description;
		$this->status = $record->status;
		$this->update(false);
		return true;
	}

	public function getRelateDatas($strict = true)
	{
		$menu = $this->menuData;
		$where = $strict ? ['controller' => $menu['controller']] : ['parent_code' => $menu['parent_code']];
		$mParams = [
			'where' => $where,
			'indexBy' => 'code',
		];
		$menus = $this->getPointModel('menu')->getInfos($mParams);
		$codes = (array) array_keys($menus);
		$params = [
			'where' => ['menu_code' => $codes, 'sort' => $this->sort],
			'select' => 'id,menu_code,method,endpoint,description,status',
		];
		return $this->getInfos($params);
	}

	public function getMenuData()
	{
		return $this->getPointModel('menu')->getInfo($this->menu_code, 'code');
	}
}
