<?php
namespace common\controllers\provider;

use Yii;
use yii\base\InvalidConfigException;

trait TraitWechat
{
    public function openLoginInfo($params)
    {
        $wechat = $this->getWechatInfo($params['code']);
        return $wechat->openLoginInfo($params['pointParam']);
    }

	public function getOpenLoginParams()
	{
		return [];
	}

    public function getIsWechat()
    {
        //return true;
        return isset($_SERVER['HTTP_USER_AGENT']) ? (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false) : false;
    }

	public function getDefaultWechat()
	{
		return $this->getPointModel('wechat')->getInfo(Yii::$app->params['currentWechatCode'], 'code');
	}
}
