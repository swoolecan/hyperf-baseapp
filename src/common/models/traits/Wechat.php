<?php

namespace common\models\traits;

use Yii;

trait Wechat
{
    public function getWechatByMerchant($merchantInfo)
    {
		if (empty($merchantInfo)) {
			return ;
		}

		$model = $this->getPointModel('wechat');
		$wechat = $model->getInfo(['where' => ['merchant_id' => $merchantInfo['id'], 'sort' => 'wechat']]);
		$wechat = empty($wechat) ? $model->getInfo(Yii::$app->params['currentWechatCode'], 'code') : $wechat;
        if (empty($wechat)) {
            $lDatas = [
                'key' => '没有对应公众号',
                'merchant_id' => 'merchant_id:' . $merchantInfo['id'] . '-' . $merchantInfo['name'],
            ];
        }
		return $wechat;

	}

    public function getPaymentWechatByMerchant($merchantInfo)
    {
		$wechat = $this->getWechatByMerchant($merchantInfo);
		if (empty($wechat)) {
			return ;
		}

		$payment = $this->getPointModel('payment')->getInfo($wechat['id'], 'plat_id');
		if (empty($payment)) {
			return null;
		}
		return $wechat;
	}

	public function getUserPlatByService($wechat, $service)
	{
		if (empty($service) || empty($wechat)) {
			return ;
		}

		$userPlat = $this->getUserPlatByUserid($wechat, $service->userId);
        if (empty($userPlat)) {
            $lDatas = [
                'key' => '客服没有关注公众号',
                'service_id' => 'service_id:' . $service['id'] . '-' . $service['name'],
            ];
            $this->_writePointLog('wechat-template', 'nowechat', $lDatas);
        }
        return $userPlat;
	}

	public function getUserPlatByUserid($wechat, $userId, $isMerchant = true)
	{
		$uField = $isMerchant ? 'muser_id' : 'user_id';
		$params = ['where' => ['plat_code' => $wechat['code'], $uField => $userId]];
		//print_r($params);
		$userPlat = $this->getPointModel('user-plat')->getInfo($params);
		return $userPlat;
	}
}
