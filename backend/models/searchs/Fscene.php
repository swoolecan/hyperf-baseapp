<?php

namespace backend\models\searchs;

use backend\models\Fscene as FsceneModel;

class Fscene extends FsceneModel
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
