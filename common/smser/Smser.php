<?php

namespace common\smser;

use Yii;
use yii\helpers\FileHelper;

class Smser extends \yii\base\Component
{
    protected $returnInfo;
    protected $smser;

    public function __construct($plat = null, $config = [])
    {
        $platInfos = require(__DIR__ . '/config/params.php');
        $plat = is_null($plat) || !in_array($plat, array_keys($platInfos)) ? 'luosimao' : $plat;

        $smser = 'common\smser\smser\\' . ucfirst($plat);
        $this->smser = new $smser($platInfos[$plat]);

        parent::__construct($config);
    }

    /**
     * 发送短信
     *
     * @param string $mobile  对方手机号码
     * @param string $content 短信内容
     * @return boolean        短信是否发送成功
     */
    public function send($mobile, $content, $sort = '')
    {
        $resultCode = $this->smser->send($mobile, $content, $sort);
		return $this->formatResult($resultCode);
    }

    public function sendCode($mobile, $sort)
    {
        $info = $this->getVerification($sort)->sendCode($mobile);
        if (!is_array($info)) {
            return $this->formatResult($info);
        }

        $cTemplates = Yii::$app->controller->getEnvironmentParams('datas', 'sms-template');
		$content = isset($cTemplates[$sort]) ? $cTemplates[$sort] : $info['code'];
		$content = str_replace('{{CODE}}', $info['code'], $content);
        $resultCode = $this->smser->send($mobile, $content, $sort);
		return $this->formatResult($resultCode);
    }

    public function checkCode($mobile, $sort, $code)
    {
        $resultCode = $this->getVerification($sort)->checkCode($mobile, $code);
		return $this->formatResult($resultCode);
    }

	protected function getVerification($sort)
	{
		return new Verification($sort);
	}

	protected function formatResult($code)
	{
		$status = $code == 'ok' ? 200 : 400;
        $codes = [
            'ok' => 'OK',
			'paramError' => '参数错误',
			'codeError' => '验证码错误',
        	'verifyError' => '验证码错误！',
        	'noCode' => '没有向该手机号发送验证码，请重新操作',
			'codeExpire' => '验证码已过期，请重新操作',
        	'timesOver' => '验证码次数超过每天的最大值，请明天再操作',
			'tooFast' => '您获取验证码频率过高，请稍后再操作',
        ];
		$message = isset($codes[$code]) ? $codes[$code] : $code;
		return ['status' => $status, 'message' => $message];
	}
}
