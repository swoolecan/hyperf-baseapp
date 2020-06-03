<?php

namespace baseapp\models\searchs;

trait PositionTrait
{
    public function rules()
    {
        return [
            [['city', 'owner_mark', 'status', 'sort'], 'safe'],
        ];
    }

    protected function _searchSourceElems()
    {
        return [
			'fields' => ['city', 'status', 'owner_mark', 'sort'],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['status', 'city', 'owner_mark', 'sort'],
			'default' => [
				'city' => ['infos' => $this->companyDatas],
			],
	    ];
    }
}
