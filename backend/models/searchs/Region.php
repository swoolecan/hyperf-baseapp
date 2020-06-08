<?php

namespace backend\models\searchs;

use backend\models\Region as RegionModel;

class Region extends RegionModel
{
    public function rules()
    {
        return [
            [['name', 'parent_code', 'level'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
		$this->parent_code = is_null($this->parent_code) ? '' : $this->parent_code;
        return [
			'fields' => ['name', 'parent_code', 'level'],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['level'],
			'form' => [],
		];
    }
}

