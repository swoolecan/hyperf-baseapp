<?php

namespace common\models\traits;

use Yii;
use yii\helpers\Inflector;

trait CommonField
{
    public function getKeyInfos($key)
    {
        $key = Inflector::id2camel($key, '_');
        $keyDatas = "{$key}Infos";
        return $this->$keyDatas;
    }

    public function getKeyName($key, $value, $datas = null)
    {
        if (is_null($datas)) {
            $infos = $this->getKeyInfos($key);
        }
        return isset($infos[$value]) ? $infos[$value] : $value;
    }

	public function getGenderInfos()
	{
		return [
			'0' => '保密', 
			'1' => '男', 
			'2' => '女'
		];
	}

    public function getClientTypeInfos()
    {
        $datas = [
            'pc' => 'PC端',
            'h5' => '移动端',
        ];
        return $datas;
    }

    public function getChannelInfos()
    {
        $datas = [
            'bd' => '百度',
            'bdxxl' => '百度信息流',
            'bdztc' => '百度直通车',
            'sg' => '搜狗',
			'zht' => '智慧推',
            'gdt' => '广点通',
            'sm' => '神马',
            'toutiao' => '今日头条',
        ];
        $datas = array_merge($datas, $this->getChannelInnerInfos());
        $datas['360'] = '360';
        return $datas;
    }    

    public function getChannelInnerInfos()
    {
        $datas = [
            'sg' => '搜狗',
            'toutiao' => '今日头条',
            'semspider' => 'SEM抓取',
			'phone400' => '400电话',
			'serviceline' => '在线客服',
			'hotline' => '网络直拨',
        ];
        return $datas;
    }

    public function getIpInfo($returnIp = false)
    {
        $ip = Yii::$app->getRequest()->getIP();
		if ($returnIp) {
			return $ip;
		}
        $city = \common\helpers\IP::find($ip);
        $city = is_array($city) ? implode('-', $city) : $city;
        return ['ip' => $ip, 'ipcity' => $city];
    }

	public function getIsDeleteInfos()
	{
		return [
			'0' => '正常',
			'1' => '已删除',
			'99' => '异常信息',
		];
	}

    public function getIsDefaultInfos()
    {
        $datas = [
            '0' => '非默认选项',
            '1' => '默认选项',
			'99' => '停用',
        ];
        return $datas;
    }

    public function getStatusInfos()
    {
        $datas = [
            '0' => '未启用',
            '1' => '正常',
			'99' => '停用',
        ];
        return $datas;
    }

	public function getBankCodeInfos()
	{
        return Yii::$app->controller->getPointParams('attribute', 'bank');
	}

    public function _getFollowStatusInfos()
    {
        return [
            '' => '未知',
            'unjoint' => '未接',
            'refuse' => '拒接',
            'hangup' => '挂断',
            'shutdown' => '关机',
            'halt' => '停机',
            'follow' => '跟进',
            'forward' => '期房',
        ];
    }

    public function _getInvalidStatusInfos()
    {
        $datas = [
            '' => '未知',
            'no_call' => '空号',
            'noneed' => '无需求',
            'booked' => '已定好',
            'no_test' => '测试',
        ];

        return $datas;
    }

    public function _getStatusInfos()
    {
        $datas = [
            '' => '未回访',
            'follow' => '跟进',
			'valid' => '有效',
            'valid-back' => '已退单',
			'valid-out' => '承接范围外-无效',
            'bad' => '废单',
        ];
        return $datas;
    }
}
