<?php

namespace backend\models\searchs;

use backend\models\FsceneUser as FsceneUserModel;

class FsceneUser extends FsceneUserModel
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
