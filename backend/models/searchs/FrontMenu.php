<?php

namespace backend\models\searchs;

use backend\models\FrontMenu as FrontMenuModel;

class FrontMenu extends FrontMenuModel
{
    public function rules()
    {
        return [
        ];
    }

    public function _searchSourceElems()
    {
		return [
		];
    }

    public function _searchSourceDatas()
    {
		return [
	    ];
    }
}
