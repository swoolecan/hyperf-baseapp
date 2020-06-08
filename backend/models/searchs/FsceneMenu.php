<?php

namespace backend\models\searchs;

use backend\models\FsceneMenu as FsceneMenuModel;

class FsceneMenu extends FsceneMenuModel
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
