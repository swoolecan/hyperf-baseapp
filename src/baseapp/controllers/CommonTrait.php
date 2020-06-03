<?php
namespace baseapp\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
use backend\models\Region;
use yii\web\Response;

trait CommonTrait
{
	public $authOptional = ['generate-code', 'check-code'];

    public function init()
    {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }

	public function actionCheckMobile()
	{
		return $this->checkCommon('mobile');
	}

	public function actionCheckCaptcha()
	{
		return $this->checkCommon('captcha');
	}

	public function actionCheckEmail()
	{
		return $this->checkCommon('email');
	}

	public function actionGenerateCode()
	{
		$data = $this->_formatInput(['mobile', 'captcha', 'type']);
		return $this->getModel()->generateCode($data);
	}

	public function actionValidateCode()
	{
		$data = $this->_formatInput(['mobile', 'code', 'type']);
		return $this->getModel()->validateCode($data);
	}

	public function actionMobileSignup()
	{
		$data = $this->_formatInput(['mobile']);
		return $this->getModel()->mobileSignup($data);
	}

	public function actionCityCode()
	{
		//$pCode = Yii::$app->request->get('id', '');
		//return $this->getModel()->getRegionInfo($pCode);
	    return ['status' => 200, 'message' => 'OK', 'datas' => $this->subInfos()];
	}

	public function actionChildren()
	{
		return $this->subInfos();
	}

    protected function subInfos()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $parentCode = Yii::$app->request->get('parent_code');
        $model = $this->getPointModel('region');
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

    protected function checkCommon($field)
    {
		$data = $this->_formatInput([$field]);
		return $this->getModel()->checkCommon($field, $data[$field]);
    }

	protected function _formatInput($fields)
	{
		$data = [];
		foreach ($fields as $field) {
			$data[$field] = trim(strip_tags(Yii::$app->request->post($field)));
			//$data[$field] = trim(strip_tags(Yii::$app->request->get($field)));
		}
		return $data;
	}
}
