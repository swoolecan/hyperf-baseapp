<?php

namespace common\smser\smser;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

/**
 * 螺丝帽(https://luosimao.com/)
 */
class Luosimao extends SmserAbstract
{
    public function send($mobile, $content, $sort)
    {
        $content = $this->_formatContent($content);
        $startTime = microtime(true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
        curl_setopt($ch, CURLOPT_URL, $this->configInfo['url']);

        curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $key = $this->configInfo['uid'] . ':key-' . $this->configInfo['key'];
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $key);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        $data = [
            'mobile' => $mobile,
            'message' => $content,
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $noSendMessage = isset(\Yii::$app->params['noSendMessage']) ? \Yii::$app->params['noSendMessage'] : false;
        $result = $noSendMessage ? true : curl_exec($ch);
        //var_dump($result);exit();
        //$res = curl_error($ch);
        curl_close($ch);

        $return = $this->_formatResult($result);
        $this->_writeLog($return, $mobile, $content, $sort, $startTime);

        $returnCode = $return['status'] || $result ? 'ok' : 'sendFail';
        return $returnCode;
    }

    /**
     * @inheritdoc
     */
    protected function _formatData($mobile, $content)
    {
        $data = [
            'uid' => $this->configInfo['uid'],
            'pwd' => $this->configInfo['pwd'],
            'mobile' => $mobile,
            'content' => $content
        ];

        return $data;
    }

    protected function _formatResult($result)
    {
        $result = json_decode($result, true);

        $return = [
            'status' => $result['error'] === 0,
            'message' => $result['msg'],
            'extinfo' => $result['error'],
        ];
        return $return;
    }
}
