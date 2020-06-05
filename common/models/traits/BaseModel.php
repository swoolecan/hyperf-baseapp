<?php

namespace common\models\traits;

use Yii;
use baseapp\behaviors\BehaviorHelper;

trait BaseModel
{
    use CommonField;
    use Curl;
	//use GoodsSku;
	use Field;
	use Format;
	use Level;
	use PHPExcel;
	use Priv;
	use Rest;
	use Notice;
	use SearchBase;
	use SearchForm;
	use SearchSearch;
	use SearchTemplate;
	//use Statistic;
	use Tool;
	use Wechat;

	public $listOperations = [];
	public $privFields;
	public $base_module;

    public function behaviors()
    {
        $behaviorCodes = array_merge(['validator', 'point'], $this->behaviorCodes);
		if ($this->hasProperty('created_at') && $this->hasProperty('updated_at') && !$this->hasProperty('noTimeBehavior')) {
			$behaviorCodes = array_merge(['timestamp'], $behaviorCodes);
		}

        $return = BehaviorHelper::behaviors($this->className(), $behaviorCodes);
        return $return;
    }

    public function getBehaviorCodes()
    {
        return [];
    }

	public function resizePic($field, $width, $height)
	{
		$str = $this->$field;
		if (empty($str)) {
			return '';
		}

		$str .= "?x-oss-process=image/resize,m_fill,w_{$width},h_{$height},limit_0/auto-orient,0/quality,q_90";
		return $str;
	}

    protected function _getCacheDatas($key)
    {
        $cache = Yii::$app->cache;
        $datas = $cache->get($key);
        return $datas;
    }

    protected function _setCacheDatas($key, $datas)
    {
        $cache = Yii::$app->cache;
        $cache->set($key, $datas);
        return $datas;
    }

    public function checkUserExist()
    {
        $result = $this->getPointModel('user')->getInfo($this->user_id);
        if (!$result) {
            $this->addError('user_id', '用户不存在');
        }
    }

    public function checkGoodsExist()
    {
        $result = $this->getPointModel('goods')->getInfo($this->goods_id);
        if (!$result) {
            $this->addError('goods_id', '商品不存在');
        }
    }

	public function dealCoupon($sort, $data)
	{
		$haveCoupon = isset(Yii::$app->params['haveCoupon']) ? Yii::$app->params['haveCoupon'] : false;
		if (empty($haveCoupon)) {
			return false;
		}
		$model = $this->getPointModel('coupon-sort');
		return $model->createCoupon($sort, $data);
	}

	public function getRedirectUrl()
	{
		return false;
	}

	public function setRedirectUrl($value)
	{
		$this->redirectUrl = $value;
	}

	protected function isResultOk($check)
	{
		return $check['status'] == 200;
	}

	public function setWechatSession($type, $value)
	{
		$session = Yii::$app->session;
		$session->set('start__session', true);
		$key = '_wechat_session_' . $type;
		$cacheKey = $key . $session->id;
		//$str = 'set' . '-' . $cacheKey . '-' . count($value) . '-' . $session->id . '-' . date('H:i:s') . "\n";
		//file_put_contents('/tmp/get.txt', $str, FILE_APPEND);
		Yii::$app->cache->set($cacheKey, $value);
		Yii::$app->session->set($key, $value);
	}

	public function getWechatSession($type)
	{
		$key = '_wechat_session_' . $type;
		$value = Yii::$app->session->get($key);
		if (is_null($value)) {
			$sId = Yii::$app->request->get('session_id', '');
			if (empty($sId)) {
				$value = null;
			} else {
		        $cacheKey = $key . $sId;
			    $value = Yii::$app->cache->get($cacheKey);
			}
		}
		if (!empty($value) && $type == 'user') {
			$value = json_decode($value, true);
        }

		return $value;
	}

	public function getMerchantUserName($view = null, $userIdField = null)
	{
		$userIdField = is_null($userIdField) ? 'user_id' : $userIdField;
		$userId = $this->getRelateField($userIdField);
		$userInfo = $this->getPointModel('user-merchant')->getInfo($userId);
		if (empty($userInfo)) {
			return $userId;
		}
		$nickname = empty($userInfo['nickname']) ? '匿名' : $userInfo['nickname'];
		return $nickname . ' (' . $this->maskMobile($userInfo['mobile']) . ')';
	}

	public function getRelateField($field)
	{
		return $this->$field;
	}

	public function fillMonthDay($data, $field = 'created_at', $ignores = ['hour'])
	{
		$self = isset($data[$field]) || (is_object($data) && $data->hasProperty($field)) ? $data[$field] : 0;
		$time = !empty($self) ? $self : Yii::$app->params['currentTime'];
		$elems = [
			'month' => 'Ym', 
			'week' => 'W', 
			'weekday' => 'N', 
			'day' => 'Ymd',
			'hour' => 'H',
		];
		foreach ($elems as $elem => $value) {
			if (in_array($elem, $ignores)) {
				continue;
			}
			$data['created_' . $elem] = date($value, $time);
		}
		return $data;
	}

	public function getLogBaseData()
	{
		$userInfo = Yii::$app->controller->userInfo;
		$operatorName = empty($userInfo) ? '' : $userInfo['name'];
		$operatorId = empty($userInfo) ? 0 : $userInfo['id'];
		return [
			'operator_id' => $operatorId,
			'operator_name' => $operatorName,
			'created_at' => Yii::$app->params['currentTime'],
		];
	}

	public function checkChange($newInfo, $oldInfo)
	{
		$str = '';
		$data = [];
		foreach ($this->monitFields as $mField) {
			$data[$mField] = $newInfo->$mField;
			if ($newInfo->$mField != $oldInfo->$mField) {
				$str .= $this->getAttributeLabel($mField) . "数据由'{$oldInfo->$mField}' 变为 '{$newInfo[$mField]}'===";
			}
		}
		if (empty($str)) {
			return false;
		}
		$data['content'] = $str;
		return $data;
	}

	public function getRegionCodeUrl()
	{
		return Yii::$app->controller->regionCodeUrl;
	}

    public function getNameUrl()
    {
		$name = empty($this->status) ? "测试用Url--{$this->name}" : $this->name;
        return "<a href='{$this->showUrl}' target='_blank'>{$name}</a>";
    }

    public function getShowUrl($isMobile = null, $siteCode = null)
    {
        $domain = $this->getValidDomain($isMobile, $siteCode);
        $path = $this->_getShowUrl();
        return $domain . $path;
    }

    public function getValidDomain($isMobile = null, $siteCode = null)
    {
        if (!is_null($siteCode)) {
            return $this->appAttr->getCurrentDomain($isMobile, $siteCode);
        }

        $siteCode = $this->appAttr->siteInfo['code'];
        if (!in_array($siteCode, $this->validDomains)) {
            $siteCode = $this->appAttr->siteInfoRelate ? $this->appAttr->siteInfoRelate['code'] : '';
        }
        $siteCode = in_array($siteCode, $this->validDomains) ? $siteCode : $this->_infocmsCode();
        return $this->appAttr->getCurrentDomain($isMobile, $siteCode);
    }

	protected function _infocmsCode()
	{
		return '';
	}

    protected function getValidDomains()
    {
        return [];
    }
}
