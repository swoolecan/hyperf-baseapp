<?php

namespace common\smser\smser;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

/**
 * 公司短信业务
 */
class Company extends SmserAbstract
{
    public function send($mobile, $content, $sort)
    {
        $content = $this->_formatContent($content);
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->configInfo['url']);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        $data = $this->_formatData($mobile, $content);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $noSendMessage = isset(\Yii::$app->params['noSendMessage']) ? \Yii::$app->params['noSendMessage'] : false;
        $result    = $noSendMessage ? false : curl_exec($ch);
        //$res  = curl_error( $ch );
        curl_close($ch);

        $return = $this->_formatResult($result);
        $this->_writeLog($return, $mobile, $content, $sort, $startTime);

        $returnCode = $return['status'] ? 'OK' : 'SMS_SEND_FAIL';
        return $returnCode;
    }

    /**
     * @inheritdoc
     */
    protected function _formatData($mobile, $content)
    {
        $data = [
            'p.uname' => $this->configInfo['uid'],
            'p.pwd' => $this->configInfo['key'],
            'p.mobiles' => $mobile,
            'p.content' => $content
        ];

        return $data;
    }

    protected function _formatResult($result)
    {
        $result = json_decode($result, true);

        $return = [
            'status' => isset($result['ret']) && $result['ret'] === 0,
            'message' => isset($result['desc']) ? $result['desc'] : '',
            'extinfo' => '',
        ];
        return $return;
    }
}
