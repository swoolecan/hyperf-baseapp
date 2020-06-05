<?php
namespace common\helpers;

use Yii;
use EasyWeChat\Factory;

class ErrorNotice
{
	public static function getApp($config)
	{
        $config = [
            'app_id' => $config['app_id'],
			'secret' => $config['secret'],
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => '/tmp/wechat.log',
            ],
        ];
        return Factory::officialAccount($config);
	}

	public static function sendNotice($exception)
	{
		$config = \common\helpers\InitFormat::getBaseParams('config/params-local.php');
		$config = $config['errorNotice'];
		$class = get_class($exception);
		if ($config['noNotice'] || in_array($class, $config['ignore']) || $exception->statusCode == '200') {
			return ;
		}
		$app = self::getApp($config);

		$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$url = substr($url, 0, 50);
        $params = [
            'template_id' => $config['template_id'],
            'data' => [
                'first' => '测试错误通知-' . $exception->getMessage(),
                'time' => date('Y-m-d H:i:s'),
                'ip_list' => Yii::$app->id,
                'sec_type' => get_class($exception),
				'remark' => $exception->getLine() . "--{$url}"  . "\n" . $exception->getFile(),
            ],
        ];
		foreach ($config['touser'] as $touser) {
			$params['touser'] = $touser;
			$app->template_message->send($params);
		}
	}
}
