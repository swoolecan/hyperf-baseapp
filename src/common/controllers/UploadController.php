<?php

namespace common\controllers;

use Yii;
use yii\web\UploadedFile;
use common\controllers\provider\TraitBase;

class UploadController extends \yii\web\Controller
{
	use TraitBase;
    public $defaultAction = 'show';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = $this->_corBehavior();
		return $behaviors;
    }

    public function actionIndex()
    {
        error_reporting(0);//E_ALL);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $table = Yii::$app->request->get('table');
        $field = Yii::$app->request->get('field');
        $id = Yii::$app->request->get('id', 0);
        if (empty($table) || empty($field)) {
            return [];
        }

        //$_FILES = Yii::$app->params['uploadTest'];
        $params = [
            'info_table' => $table,
            'info_field' => $field,
            'info_id' => intval($id),
        ];
        $action = $this->getInputParams('action');
        if ($action == 'show') {
            return $this->getInfos($params);
        }

        $model =  $this->getAttachment($params);
        $files = UploadedFile::getInstances($model, 'files');
        $model->file = isset($files[0]) ? $files[0] : null;
        if ($model->save()) {
            $baseName = substr($model->name, 0, strrpos($model->name, '.'));
            $data = [
                'status' => '200200',
                'id' => $model->id,
                'name' => $model->name,
                'size' => $model->size,
                'filename' => $baseName,
                'orderlist' => 0,
                'description' => $baseName,
                'url' => $model->getUrl(),
            ];
        } else {
            $message = array_pop($model->getFirstErrors());
            $data = [
                'status' => '400400',
                'message' => $message,
            ];
        }
        $data = ['files' => [$data]];
        return $data;
    }

    protected function getInfos($params)
    {
        if (empty($params['info_id'])) {
            return [];
        }

        $condition = [
            'info_table' => $params['info_table'],
            'info_field' => $params['info_field'],
            'info_id' => $params['info_id'],
            'in_use' => 1,
        ];
        $infos = $this->getAttachment()->find()->where($condition)->orderBy(['orderlist' => SORT_DESC])->all();
        $datas = [];
        foreach ($infos as $key => $info) {
            $url = $info->getUrl();
            $data = $info->toArray();
            $data['url'] = $url;
            $datas[] = $data;
        }
        return ['files' => $datas];
    }

    public function actionShow($id)
    {
        $model = $this->getAttachment()->getInfo($id);;
        $response = Yii::$app->getResponse();
		$path = $model->path;
		$isDown = $this->getInputParams('download');
		$params = [
            'mimeType' => $model->type,
            'fileSize' => $model->size,
            'inline' => $isDown ? false : true
        ];
        return $response->sendFile($path, $model->name, $params);
    }
}
