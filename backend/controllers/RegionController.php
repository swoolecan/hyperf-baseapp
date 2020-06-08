<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use Overtrue\Pinyin\Pinyin;

class RegionController extends Controller
{
	public function actionChildren()
	{
		return $this->subInfos();
	}

    protected function subInfos()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $parentCode = Yii::$app->request->get('parent_code');
        $model = $this->model;
        $infos = $model->subInfos($parentCode);
        $isDirect = $model->isDirect($parentCode);
        $datas = [];
        if ($isDirect) {
            foreach ($infos as $info) {
                $subDatas = (array) $model->subInfos($info['code']);
                $datas = array_merge($datas, $subDatas);
            }
        } else {
            $datas = $infos;
        }

        $datas = ArrayHelper::map($datas, 'code', 'name');
        //print_r($datas);
        return $datas;
    }

    public function _addData()
    {
        return [
            'parent_code' => Yii::$app->request->get('parent_code'),
        ];
    }

	public function getValidActions()
	{
		return ['listinfo', 'children'];
	}
}
