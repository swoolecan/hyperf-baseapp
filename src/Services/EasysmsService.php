<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Services;

use Overtrue\EasySms\EasySms;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CachePut;

class EasysmsService extends AbstractService
{
	protected $isTest;
    protected $createInfo;

    protected $returnInfo;

    public function __construct()
    {
    }

    public function getConfig($key = null)
    {
        static $config;
        if (empty($config)) {
            $config = $this->config->get('easysms');
        }
        if (empty($key)) {
            return $config;
        }
        return $config[$key];
    }

    /**
     * 发送短信
     *
     * @param string $mobile  对方手机号码
     * @param string $content 短信内容
     * @return boolean        短信是否发送成功
     */
    public function send($mobile, $templateCode, $data)
    {
		$content = $this->formatContent($templateCode, $data);
        $easySms = new EasySms($this->configInfo);
        $r = $easySms->send($mobile, $content);
        //var_dump($r);
        return $r;
    }

	public function sendCode($data)
	{
        $this->configInfo = $this->config->get('easysms');
        $mobile = $data['mobile'];
        $type = $data['type'];
        $typeConfig = $this->configInfo['verifyCode'][$type];
        $infoExist = $this->getCodeInfo($mobile . '-' . $type);
        $check = $this->checkSend($infoExist, $typeConfig);
        if ($check !== true) {
            return $check;
        }

        $param = [
            //'mobile' => $mobile, 
            'type' => $type, 
            'infoExist' => $infoExist,
            'typeConfig' => $typeConfig,
        ];
        $this->createInfo = $this->_createCode($param);
        $this->cacheCode($mobile . '-' . $type);
        return $this->send($data['mobile'], $data['template'], ['code' => $this->createInfo['code']]);
    }

	public function validateCode($data)
	{
        $type = $data['type'];
        $typeConfig = $this->configInfo['verifyCode'][$type];
        $info = $this->getCodeInfo($data['mobile'] . '-' . $type);

		if (empty($info)) {
			return ['code' => 400, 'message' => '没有向该手机号发送验证码，请重新操作'];
		}

		if ($info['code'] != $data['code']) {
			$message = $this->isTest ? 'codeError-' . $info['code'] : '验证码错误';
			return ['code' => 400, 'message' => $message];
		}

        $expire = isset($typeConfig['expire']) ? $this->typeConfig['expire'] : 300;
        if (time() > $info['updatedAt'] + $expire) {
			return ['code' => 400, 'message' => '您的验证码已经过期，请重新获取'];
        }
        return ['code' => 200, 'message' => 'success'];
	}

    protected function _createCode($param)
    {
        $typeConfig = $param['typeConfig'];
        $infoExist = $param['infoExist'];
        $length = isset($typeConfig['length']) ? $typeConfig['length'] : 4;
        $length = is_array($length) ? mt_rand($length[0], $length[1]) : $length;

        $code = '';
        for($i = 0; $i < $length; $i++) {
            $code .= mt_rand(0, 9);
        }

        if (!empty($infoExist) && date('Ymd', $infoExist['createdAt']) != date('Ymd', time())) {
            $infoExist['createdAt'] = time();
            $infoExist['count'] = 0;
        }

        return [
            'code' => $code,
            'createdAt' => isset($infoExist['createdAt']) ? $infoExist['createdAt'] : time(),
            'updatedAt' => time(),
            'sendTimes' => isset($infoExist['sendTimes']) ? $infoExist['sendTimes'] + 1 : 1,
            'count' => 0,
        ];
    }

    /**
     * @CachePut(prefix="sms-", group="filesys")
     */
    protected function cacheCode($key)
    {
        return $this->createInfo;
    }

    /**
     * @Cacheable(prefix="sms-", group="filesys")
     */
    protected function getCodeInfo($key)
    {
        return null;
    }

    protected function checkSend($info, $typeConfig)
    {
        if (empty($info)) {
			return true;
        }

        $sendTimes = isset($this->typeConfig['sendTimes']) ? $this->typeConfig['sendTimes'] : 5;
        if ($info['sendTimes'] > $sendTimes) {
            return ['code' => 400, 'message' => '您今天获取验证的次数已达到上限，请您暂停再操作'];
        }

        $sleep = isset($typeConfig['sleep']) ? $typeConfig['sleep'] : 60;
        $diff = time() - $info['updatedAt'];
        if ($diff < $sleep) {
            $remain = $sleep - $diff;
            return ['code' => 400, 'message' => "请您{$remain}秒后，再获取验证码"];
        }

        return true;
	}

    /*protected function _writeLog($return, $mobile, $content, $sort, $startTime)
    {
        $endTime = microtime(true);
        $timeUsed = number_format($endTime - $startTime, 3);
        $currentDate = date('Y-m-d H:i:s');
        $content = "==={$mobile}==={$currentDate}===\r\n"
            . "---{$timeUsed}---{$return['message']}---{$return['extinfo']}---\r\n"
            . "---{$content}---\r\n\r\n";

        $logFile = \Yii::getAlias('@runtime') . '/logs/sms/' . date('Y-m-d') . '/' . $sort;
        $logFile .= $return['status'] ? '_success.log' : '_error.log';
        $path = dirname($logFile);
        if (!is_dir($path)) {
            \yii\helpers\FileHelper::createDirectory($path);
        }
        file_put_contents($logFile, $content, FILE_APPEND);

        return true;
    }*/

    protected function formatContent($template, $data)
    {
        $result = [
            'content'  => '您的验证码为: 6379',
            'template' => 'SMS_001',
            'data' => [
                'code' => 6379
            ],
        ];
        return $result;
    }
}
