<?php

namespace common\smser\smser;

abstract class SmserAbstract
{
    protected $configInfo;

    public function __construct($configInfo)
    {
        $this->configInfo = $configInfo;
    }

    public function send($mobile, $content, $sort)
    {
        $startTime = microtime(true);

        $data = $this->_formatData($mobile, $content, $sort);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->configInfo['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $noSendMessage = isset(\Yii::$app->params['noSendMessage']) ? \Yii::$app->params['noSendMessage'] : false;
        $result    = $noSendMessage ? false : curl_exec($ch);
        curl_close($ch);

        $return = $this->_formatResult($result);
        $this->_writeLog($return, $mobile, $content, $sort, $startTime);

        $returnCode = $return['status'] ? 'ok' : 'sendFail';
        return $returnCode;
    }

    protected function _writeLog($return, $mobile, $content, $sort, $startTime)
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
    }

    protected function _formatContent($content)
    {
		if (strpos($content, '】') !== false) {
			return $content;
		}
        $signName = isset(\Yii::$app->params['smsSignName']) ? \Yii::$app->params['smsSignName'] : '';

        return $content . $signName;
    }
}
