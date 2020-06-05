<?php

namespace baseapp\models\searchs;

trait TagInfoTrait
{
    public function rules()
    {
        return [
            [['name', 'info_type', 'info_id', 'tag_code'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
        return [
			'fields' => ['info_type', 'info_id', 'tag_code'],
			'default' => [
				//'name' => ['sort' => 'like'],
			],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['info_type'],
			'form' => ['name'],
		];
    }
}
