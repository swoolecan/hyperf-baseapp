<?php

namespace baseapp\behaviors;

use yii\base\Behavior;
use common\smser\Smser;
use yii\helpers\Inflector;

class ValidatorBehavior extends Behavior
{
    public function checkMobileCode($data)
    {
        $smser = new Smser();
        $check = $smser->checkCode($data['mobile'], $data['type'], $data['code']);
        return $check;
    }

    public function checkMobile($value, $allowEmpty = false)
    {
        $param = [
            'code' => 'mobile',
            'name' => '手机号',
            'class' => '\common\validators\MobileValidator',
        ];
        return $this->_check($param, $value, $allowEmpty);
    }

    public function checkEmail($value, $allowEmpty = false)
    {
        $param = [
            'code' => 'email',
            'name' => '邮箱',
            'class' => '\yii\validators\EmailValidator',
        ];
        return $this->_check($param, $value, $allowEmpty);
    }

    public function checkCaptcha($value, $allowEmpty = false)
    {
        $param = [
            'code' => 'captcha',
            'name' => '图形验证码',
            'class' => '\yii\captcha\CaptchaValidator',
        ];
        return $this->_check($param, $value, $allowEmpty);
    }

    protected function _check($param, $value, $allowEmpty)
    {
        if (empty($value) && $allowEmpty) {
            return ['status' => 200, 'message' => 'OK'];
        }

        if (empty($value)) {
            return ['status' => 400, 'message' => $param['name'] . '不能为空'];
        }

        $validator = new $param['class'];
        $valid =  $validator->validate($value);
        if (empty($valid)) {
            return ['status' => 400, 'message' => $param['name'] . '有误'];
        }
        return ['status' => 200, 'message' => 'OK'];
    }

    public function checkId($idcard)
    {
		$idcard = trim($idcard);
		$len = strlen($idcard);
		//$idcard = $len == 15 ? $this->idcard15to18($idcard) : $idcard;
		if (strlen($idcard) != 18) { 
            //$specials = ['996', '997', '998', '999']; // 身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
		    //$str = substr($idcard, 12, 3);
            //$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
            $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
			//echo '---' . $this->idcardVerifyNumber($idcard) . '==';
            $idcard = $idcard . $this->idcardVerifyNumber($idcard);
		}
		if (strlen($idcard) != 18) {
			return false;
		}
        $idcardBase = substr($idcard, 0, 17);
		$verify = $this->idcardVerifyNumber($idcardBase);
		$validStr = strtoupper(substr($idcard, 17, 1));
		//var_dump($verify); var_dump($validStr);exit();
		return $verify === $validStr;
    }

	public function idcardVerifyNumber($idcardBase)
	{
        $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2]; // debug 加权因子
        $verifyNumberList = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2']; // debug 校验码对应值
        $checksum = 0;
        for ($i = 0; $i < strlen($idcardBase); $i++){
            $checksum += substr($idcardBase, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verifyNumber = $verifyNumberList[$mod];
        return $verifyNumber;
    }

    public function checkCommon($elem, $param)
    {
        $method = 'check' . ucfirst(Inflector::id2camel($elem, '_'));
        $check = $this->$method($param);
        if ($check['status'] !== 200) {
            $this->owner->addError($elem, $check['message']);
			return $check;
        }
		return ['status' => 200, 'message' => 'OK'];
    }
}
