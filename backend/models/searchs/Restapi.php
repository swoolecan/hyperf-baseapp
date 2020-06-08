<?php

namespace backend\models\searchs;

use backend\models\Restapi as RestapiModel;

class Restapi extends RestapiModel
{
    public function rules()
    {
        return [
            [['sort', 'module', 'name', 'menu_code', 'is_lock'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
		if (empty($this->name)) {
			$this->name = null;
		}
		if (empty($this->menu_code)) {
			$this->menu_code = null;
		}
		if (!empty($this->module)) {
			$params = [
				'where' => ['module' => $this->module], 
				'indexBy' => 'code',
			];
			$menus = (array) $this->getPointModel('menu')->getInfos($params);
			$this->menu_code = array_keys($menus);
		}
        return [
			'fields' => ['menu_code', 'is_lock', 'name', 'sort'],
			'default' => [
				//'menu_code' => ['sort' => 'like'],
				'name' => ['sort' => 'like'],
			],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['module', 'is_lock', 'sort'],
			'form' => ['menu_code', 'name'],
			'default' => [
			],
	    ];
    }
}
