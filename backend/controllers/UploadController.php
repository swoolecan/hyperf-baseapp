<?php

namespace backend\controllers;

use Yii;
use yii\web\UploadedFile;
use common\controllers\provider\TraitBase;
use baseapp\models\Attachment;

class UploadController extends Controller
{
	use TraitBase;
    public $enableCsrfValidation = false;
    public $defaultAction = 'show';
	public $attachmentCode;

	public function init()
	{
		parent::init();
		$attachmentCode = $this->getInputParams('attachment_code');
		if (empty($attachmentCode)) {
			exit('参数有误');
		}
		$this->attachmentCode = $attachmentCode;
	}

	public function actions()
	{
		return array_merge(parent::actions(), [
            'upeditor' => [
                'class' => 'common\ueditor\UEditorAction',
            ],  
		]);
	}

    public function getAttachment($params = [])
    {
        //return $this->getPointModel($this->attachmentCode, true, $params);
		$modelMid = $this->getPointModel('attachment');
		$model = $modelMid->initMark($this->attachmentCode);
		if (!empty($params)) {
			foreach ($params as $field => $value) {
				$model->$field = $value;
			}
		}
		return $model;
    }

    public function actionIndex()
    {
        error_reporting(0);//E_ALL);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $table = $this->getInputParams('table');
        $field = $this->getInputParams('field');
        $id = $this->getInputParams('id');
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
        $model = $this->getAttachment();
        $infos = $model->find()->where($condition)->orderBy(['orderlist' => SORT_DESC])->all();
        $datas = [];
        foreach ($infos as $key => $info) {
            $info->urlPrefix = $model->urlPrefix;
            $url = $info->getUrl();
            $data = $info->toArray();
            $data['url'] = $url;
            $datas[] = $data;
        }
        return ['files' => $datas];
    }

    public function actionShow($id = null)
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

	public function getValidActions()
	{
		return ['index', 'show'];
	}

    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = $this->_corBehavior();
		return $behaviors;
	}*/
}
