<?php

namespace backend\models\searchs;

use backend\models\Menu as MenuModel;

class Menu extends MenuModel
{
    public function rules()
    {
        return [
            [['module', 'code'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
		if (empty($this->code)) {
			$this->code = null;
		}
        return [
			'fields' => ['module', 'code'],
			'default' => [
				'code' => ['sort' => 'like'],
			],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['module'],
			'form' => ['code'],
	    ];
    }
}
