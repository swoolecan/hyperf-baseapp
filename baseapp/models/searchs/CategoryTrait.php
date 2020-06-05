<?php

namespace baseapp\models\searchs;

trait CategoryTrait
{
    public function rules()
    {
        return [
            [['parent_code'], 'safe'],
        ];
    }

    protected function _searchSourceElems()
    {
        return [
			'fields' => ['parent_code'],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => [''],
			'default' => [
				//'city' => ['infos' => $this->companyDatas],
			],
	    ];
    }
}
