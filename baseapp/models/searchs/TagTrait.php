<?php

namespace baseapp\models\searchs;

trait TagTrait
{
    public function rules()
    {
        return [
            [['id', 'name', 'status'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
        return [
			'fields' => ['id', 'status', 'name'],
			'default' => [
				'name' => ['sort' => 'like'],
			],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['status'],
			'form' => ['name'],
		];
    }
}
