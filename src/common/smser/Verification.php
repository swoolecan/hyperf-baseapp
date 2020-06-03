<?php

namespace common\smser;

use Yii;

class Verification extends \yii\base\BaseObject
{
	protected $sort;
	protected $isTest;
    protected $configInfo;
    protected $currentTime;

    public function __construct($sort, $config = [])
    {
        $configInfos = require(__DIR__ . '/config/params-verification.php');
		$this->isTest = isset($configInfos['isTest']) ? $configInfos['isTest'] : false;
		$this->sort = $sort;
		$this->configInfo = isset($configInfos[$sort]) ? $configInfos[$sort] : null;
        $this->currentTime = time();

        parent::__construct($config);
    }

    public function sendCode($mobile)
    {
		if (empty($this->configInfo)) {
			return 'paramError';
		}

        $oldInfo = $this->_getCode($mobile);
		$checkInfo = $this->_checkSend($oldInfo);
		if ($checkInfo !== true) {
			return $checkInfo;
		}

        if (!empty($oldInfo) && date('Ymd', $oldInfo['createdAt']) != date('Ymd', $this->currentTime)) {
            $oldInfo['createdAt'] = $this->currentTime;
            $oldInfo['count'] = 0;
        }

        $info = $this->_generateCode($mobile, $oldInfo);
        return $info;
    }

    public function checkCode($mobile, $code)
    {
		if (empty($this->configInfo)) {
			return 'paramError';
		}

        $info = $this->_getCode($mobile);
		$checkInfo = $this->_checkCode($info, $code);
		return $checkInfo;
    }

    protected function _getCode($mobile)
    {
        $cache = Yii::$app->cache;
        $key = "sms_{$mobile}_{$this->sort}";
        $info = $cache->get($key);
        $info = empty($info) ? [] : $info;

        return $info;
    }

    protected function _checkSend($info)
    {
        if (empty($info)) {
			return true;
        }

        $sendTimes = isset($this->configInfo['sendTimes']) ? $this->configInfo['sendTimes'] : 5;
        if ($info['sendTimes'] > $sendTimes) {
            //return 'timesOver';
        }

        $sleep = isset($this->configInfo['sleep']) ? $this->configInfo['sleep'] : 60;
        if ($this->currentTime - $info['updatedAt'] < $sleep) {
            return 'tooFast';
        }

        return true;
	}

	protected function _checkCode($info, $code)
	{
		if (empty($info)) {
			return 'noCode';
		}

		if ($info['code'] != $code) {
			$return = $this->isTest ? 'codeError-' . $info['code'] : 'codeError';
			return $return;
		}
        $expire = isset($this->configInfo['expire']) ? $this->configInfo['expire'] : 300;
        if ($this->currentTime > $info['updatedAt'] + $expire) {
            return 'codeExpire';
        }
        return 'ok';
    }

    protected function _generateCode($mobile, $oldInfo = [])
    {
        $cache = Yii::$app->cache;

        $length = isset($this->configInfo['length']) ? $this->configInfo['length'] : 4;
        $length = is_array($length) ? mt_rand($length[0], $length[1]) : $length;

        $code = '';
        for($i = 0; $i < $length; $i++) {
            $code .= mt_rand(0, 9);
        }
        $info = [
            'code' => $code,
            'createdAt' => isset($oldInfo['createdAt']) ? $oldInfo['createdAt'] : $this->currentTime,
            'updatedAt' => $this->currentTime,
            'sendTimes' => isset($oldInfo['sendTimes']) ? $oldInfo['sendTimes'] + 1 : 1,
            'count' => 0,
        ];
        $key = "sms_{$mobile}_{$this->sort}";
        $cache->set($key, $info, 86400);

        return $info;
    }
}
