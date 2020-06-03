<?php
namespace common\controllers\provider;

use Yii;
use yii\helpers\Json;

trait TraitData
{
    public function getPointDatas($code, $params = [], $forceNew = false)
    {
        $datas = $this->getPointModel($code)->getPointDatas($code, $params, $forceNew);
        return $datas;
    }

    public function appContextDatas($code, $sort = 'app', $indexName = null)
    {
        $data = $sort == 'app' ? (isset(Yii::$app->params[$code]) ? Yii::$app->params[$code] : '') : $this->$code;
        if (is_null($indexName)) {
            return $data;
        }
        return isset($data[$indexName]) ? $data[$indexName] : '';
    }

	public function getRegionInfos($parentCode = '')
	{
		return $this->getPointModel('region')->getRegionInfos($parentCode);
	}

	public function setCurrentGlobal($key, $value)
	{
		$key = 'current' . ucfirst($key);
		Yii::$app->params[$key] = $value;
	}

	protected function websiteGlobalData()
	{
		$point = $this->getInputParams('point_website_code', 'postget');
        $session = Yii::$app->session;
		if (empty($point)) {
		    $point = isset($session['point_website_code']) ? $session['point_website_code'] : '';
		}
		$exist = $this->appContextDatas('websiteGlobal');
		if (!empty($exist) && empty($point)) {
			return $exist;
		}

		$model = $this->getPointModel('website');
		$default = $this->appContextDatas('currentWebsite');
		$point = !empty($point) ? $point : $default;
		$data = $model->getInfo($point, 'code');
		$data = empty($merchant) ? $model->getInfo($default) : $data;
		$session['point_website_code'] = $data['code'];
		return $data;
	}

	protected function merchantGlobalData()
	{
		$point = $this->getInputParams('point_merchant_id', 'postget');
		$exist = $this->appContextDatas('merchantGlobal');
		if (!empty($exist) && empty($point)) {
			return $exist;
		}

		$model = $this->getPointModel('merchant');
		$default = $this->appContextDatas('currentMerchant');
		$point = !empty($point) ? $point : $default;
		$data = $model->getInfo($point);
		$data = empty($merchant) ? $model->getInfo($default) : $data;
		return $data;
	}

    public function wechatGlobalData()
    {
        //$session = Yii::$app->session;
        $point = $this->getInputParams('point_wechat_code', 'postget');
		$exist = $this->appContextDatas('wechatGlobal');
		if (!empty($exist) && empty($point)) {
			return $exist;
		}

		$model = $this->getPointModel('wechat');
        $data = empty($point) ? null : $model->getInfo($point, 'code');
		if (!empty($data)) {
			return $data;
		}
		$merchant = $this->appContextDatas('merchantGlobal');
		$data = !empty($merchant) ? $merchant->getWechatByMerchant($merchant) : null;
		if (!empty($data)) {
			return $data;
		}

		$default = $this->appContextDatas('currentWechat');
		$data = $model->getInfo($default, 'code');
		return $data;
    }

    public function shareGlobalData()
    {
		$point = $this->_shareData;
		if (isset($point['description'])) {
			$point['desc'] = $point['description'];
			unset($point['description']);
		}
		$default = [
            'record_type' => '',
            'record_id' => '',
            'title' => $this->getSiteElem('name'),
			'keyword' => $this->getSiteElem('name'),
            'desc' => $this->getSiteElem('description'),
            'link' => Yii::getAlias('@homeurl'),//$app->request->absoluteUrl,
            'imgUrl' => $this->getSiteElem('logoUrl'),
            'type' => '', // 分享类型,music、video或link，不填默认为link  for wechat
            'dataUrl' => '', // 如果type是music或video，则要提供数据链接，默认为空 for wechat
        ];
		$data = array_merge($default, $point);
        $queryStr = http_build_query($data);
        $data['queryStr'] = $queryStr;
        
		return $data;
        //return Json::encode($data);
    }
}
