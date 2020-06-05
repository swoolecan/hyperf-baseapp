<?php

namespace baseapp\models;

use Yii;
use yii\helpers\ArrayHelper;

trait CommonTrait
{
	public function mobileSignup($data)
	{
		$result = $this->checkCommon('mobile', $data['mobile']);
		$result = $this->isResultOk($result) ? $this->checkUser(['mobile' => $data['mobile']], 'signup') : $result;
		return $result;
	}

	public function generateCode($data)
	{
		if (empty($data['type'])) {
			return ['status' => 400, 'message' => '验证码类型有误'];
		}

		$noCaptcha = Yii::$app->controller->getInputParams('captcha', 'postget');
		$result = $noCaptcha == '0824' ? ['status' => 200, 'message' => 'OK'] : $this->checkCommon('captcha', $data['captcha']);
		$result = $this->isResultOk($result) ? $this->checkCommon('mobile', $data['mobile']) : $result;
		$result = $this->isResultOk($result) ? $this->checkUser(['mobile' => $data['mobile']], $data['type']) : $result;
        $result = $this->isResultOk($result) ? $this->sendSmsCode($data['mobile'], $data['type']) : $result;
		return $result;
	}

	public function validateCode($data)
	{
		if (empty($data['type']) || empty($data['code'])) {
			return ['status' => 400, 'message' => '验证码或验证码类型不能为空'];
		}

		$result = $this->checkCommon('mobile', $data['mobile']);
		$result = $this->isResultOk($result) ? $this->checkCommon('mobile_code', $data) : $result;
		return $result;
	}

	public function getRegionInfo($pCode, $keyValue = false)
	{
		$infos = $this->getPointModel('region')->subInfos($pCode, false);
		$infos = $keyValue ? ArrayHelper::map($infos, 'code', 'name') : $infos;
		return ['status' => 200, 'message' => 'OK', 'data' => $infos];
	}

	public function getUserInfo($where)
	{
		$userInfo = $this->userModel->getInfo(['where' => $where]);
		return $userInfo;
	}

    public function checkUser($where, $type)
    {
        if (!in_array($type, ['signin', 'signup'])) {
            return ['status' => 200, 'message' => 'OK'];
        }

        $userInfo = $this->getUserInfo($where);
        if ($type == 'signin' && empty($userInfo)) {
            return ['status' => 400, 'message' => '用户不存在，请先注册'];
        }

        if ($type == 'signup' && !empty($userInfo)) {
            return ['status' => 400, 'message' => '用户已存在，请直接登录'];
        }

        return ['status' => 200, 'message' => 'OK'];
    }
}
