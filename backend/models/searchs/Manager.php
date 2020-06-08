<?php

namespace backend\models\searchs;

use backend\models\Manager as ManagerModel;

/**
 * ManagerSearch represents the model behind the search form about `backend\models\Manager`.
 */
class Manager extends ManagerModel
{
    public function scenarios()
    {
        return ['default' => ['name', 'created_at_start', 'created_at_end', 'status']];
    }
    public function rules()
    {
        return [
            [['name', 'created_at_start', 'created_at_end', 'status'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
        return [
			'fields' => ['name', 'status', 'created_at'],
        ];
    }

    public function _searchSourceDatas()
    {
		return [
			'list' => ['status'],
			'form' => ['name', 'created_at'],
		];
    }
}
