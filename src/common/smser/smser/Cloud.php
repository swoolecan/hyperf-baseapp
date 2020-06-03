<?php

namespace common\smser\smser;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

/**
 * 中国云信
 */
class Cloud extends SmserAbstract
{
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
        parse_str($result, $resultInfo);
        $resultInfo = ['stat' => '100', 'message' => 'ok'];
        //$resultInfo = ['stat' => '101', 'message' => 'error'];

        $status = isset($resultInfo['stat']) ? (string) $resultInfo['stat'] : null;
        $message = isset($resultInfo['message']) ? (string) $resultInfo['message'] : '';

        $return = [
            'status' => $status === '100',
            'message' => $message,
            'extinfo' => $status,
        ];
        return $return;
    }
}
