<?php

namespace common\models\traits;

use Yii;
use common\smser\Smser;

trait Notice
{
    public function sendSmsBase($mobile, $content, $sort)
    {
        $smser = new Smser();
        return $smser->send($mobile, $content, $sort);
    }

    public function sendSmsCode($mobile, $sort)
    {
        $smser = new Smser();
        return $smser->sendCode($mobile, $sort);
    }

	public function sendTemplateMessage($wechat, $code, $params)
	{
		$tModel = $this->getPointModel('template-message')->getInfo(['where' => ['code' => $code, 'plat_code' => $wechat['code']]]);
		if (empty($tModel)) {
			$datas['dataStr'] = serialize($params);
            $datas['key'] = '没有指定的模板-' . $code . '-' . $wechat['name'];
            $this->_writePointLog('wechat-template', 'no-template', $datas);
			return ;
		}
		$request = Yii::$app->request;
		$isTest = is_callable($request, 'get') ? $request->get('test_wechat') : false;
		if ($isTest) {
            print_r($params);
			return ;
		}
		$noWechatMessage = isset(Yii::$app->params['noWechatMessage']) ? Yii::$app->params['noWechatMessage'] : false;
		if ($noWechatMessage) {
			echo 'eee';exit();
			return ;
		}
		return $tModel->sendMessage($params);
	}

    public function _neworderFormatTemplate($type, $data, $urlBase = '')
    {
		$notices = $this->appAttr->getEnvironmentParams('datas', 'notice-wechat');
        $notice = !isset($notices['neworder']) ? false : $notices['neworder'];
        $notice = empty($notice) ? $notice : (isset($notice[$type]) ? $notice[$type] : false);
        if (empty($notice)) {
            return false;
        }
        $tField = $notice['tradeDateTime'];
        $time = $this->formatTimestamp($data[$tField]);
        $orderData = '';
        //print_r($notice);exit();
        foreach ($notice['orderItemData'] as $field) {
            $orderData .= $data[$field] . ' / ';
        }
        $urlStr = $notice['url'];
        $url = str_replace('{{ID}}', $data['id'], $urlStr);

        $data = [
            'first' => $notice['first'],
            'tradeDateTime' => $time,
            'orderType' => $notice['orderType'],
            'customerInfo' => $data[$notice['customerInfo']],
            'orderItemName' => $notice['orderItemName'],
            'orderItemData' => "{$orderData};\n",
            'remark' => $notice['remark'],
        ];
        //print_r($data);exit();
        return ['url' => $url, 'data' => $data];
    }
}
