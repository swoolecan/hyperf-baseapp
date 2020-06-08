<?php

namespace backend\models\searchs;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use backend\models\Managerlog as ManagerlogModel;

class Managerlog extends ManagerlogModel
{
    public function rules()
    {
        return [
            [['manager_id', 'created_at_start', 'created_at_end'], 'safe'],
        ];
    }

    public function _searchSourceElems()
    {
        return [
			'field' => ['manager_id', 'created_at'],
        ];
    }

    public function _searchSourceDatas()
    {
        $managerInfos = ArrayHelper::map(\backend\models\Manager::find()->all(), 'id', 'name');
		return [
			'list' => ['manager_id'],
			'form' => ['created_at'],
			'default' => [
				'manager_id' => ['infos' => $managerInfos]
			]
		];
    }
}
