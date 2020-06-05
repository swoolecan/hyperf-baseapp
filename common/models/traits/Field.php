<?php

namespace common\models\traits;

use Yii;
use yii\helpers\Html;

trait Field
{
    protected function _getTemplateFields()
    {
		$fields = array_keys(static::_getFieldName());
		$points = $this->_getTemplatePointFields();
		if (isset($points['extFields'])) {
			$fields = array_merge($fields, $points['extFields']);
			unset($points['extFields']);
		}
		if (isset($points['listNo'])) {
			foreach ($points['listNo'] as $lField) {
				$listNo = ['listNo' => true];
				$points[$lField] = isset($points[$lField]) ? array_merge($points[$lField], $listNo) : $listNo;
			}
			unset($points['listNo']);
		}
        return $this->_fieldFormatTemplates($fields, $points);
    }

	protected function _getTemplatePointFields()
	{
		return [];
	}

    protected function _fieldFormatTemplates($fields, $defaults = [])
    {
		$return = [];
		$datas = $this->_fieldTemplates();
		foreach ($fields as $field) {
			$data = isset($datas[$field]) ? $datas[$field] : ['type' => 'common'];
			$data = isset($defaults[$field]) ? array_merge($data, $defaults[$field]) : $data;
			if (isset($data['alias'])) {
				$field = $data['alias'];
				unset($data['alias']);
			}
			$return[$field] = $data;
		}

		return $return;
	}

	protected function _fieldTemplates()
	{
		return array_merge([
            'region_code' => ['type' => 'point', 'table' => 'region', 'pointField' => 'code'],
            'city_code' => ['type' => 'point', 'table' => 'region', 'pointField' => 'code'],
            'province_code' => ['type' => 'point', 'table' => 'region', 'pointField' => 'code'],
            'county_code' => ['type' => 'point', 'table' => 'region', 'pointField' => 'code'],
            'city' => ['type' => 'point', 'table' => 'company', 'pointField' => 'code'],

            'account_target' => ['type' => 'point', 'table' => 'bank-account', 'pointField' => 'code'],
            'merchant_id' => ['type' => 'point', 'table' => 'merchant'],
            'supplier_id' => ['type' => 'point', 'table' => 'merchant'],
            'teamwork_id' => ['type' => 'point', 'table' => 'teamwork'],
            'service_id' => ['type' => 'point', 'table' => 'service'],
            //'service_id' => ['type' => 'inline', 'method' => 'getServiceName'],
            'saleman_id' => ['type' => 'inline', 'method' => 'getSalemanName'],
            'scene_id' => ['type' => 'point', 'table' => 'scene'],
            'role' => ['type' => 'point', 'table' => 'role', 'pointField' => 'code'],
            'gender' => ['type' => 'key'],

            'account_id' => ['type' => 'point', 'table' => 'account-spread'],
            'template_code' => ['type' => 'point', 'table' => 'template', 'pointField' => 'code'],
            'plan_id' => ['type' => 'point', 'table' => 'plan'],
			'keyword_id' => ['type' => 'point', 'table' => 'keyword', 'pointName' => 'keyword'],

            'wechat_id' => ['type' => 'point', 'table' => 'wechat'],
            'plat_code' => ['type' => 'point', 'table' => 'plat', 'pointField' => 'code'],
            'house_id' => ['type' => 'point', 'table' => 'house-decoration'],

            'visitor_id' => ['type' => 'point', 'table' => 'visitor', 'pointName' => 'nickname'],

            'category_code' => ['type' => 'point', 'table' => 'category-shop', 'pointField' => 'code'],
            'website_id' => ['type' => 'point', 'table' => 'website'],
			'website_goods_id' => ['type' => 'point', 'table' => 'website-goods'],
			'website_sku_id' => ['type' => 'inline', 'method' => 'getWebsiteSkuStr'],
            'warehouse_id' => ['type' => 'point', 'table' => 'warehouse'],
            'goods_id' => ['type' => 'point', 'table' => 'goods'],
            'skuv1' => ['type' => 'point', 'table' => 'attribute-value'],
            'skuv2' => ['type' => 'point', 'table' => 'attribute-value'],
			//'user_id' => ['type' => 'point', 'table' => 'user-merchant', 'nameField' => 'truename'],

            'sku_id' => ['type' => 'inline', 'method' => 'getSkuStr'],
            'user_id' => ['type' => 'inline', 'method' => 'getMerchantUserName'],
			//'mobile' => ['type' => 'inline', 'method' => 'maskMobileView'],

            'created_at' => ['type' => 'timestamp'],
            'callback_next' => ['type' => 'timestamp'],
            'updated_at' => ['type' => 'timestamp'],
            'use_at' => ['type' => 'timestamp'],
            'signup_at' => ['type' => 'timestamp'],
            'start_at' => ['type' => 'timestamp'],
            'end_at' => ['type' => 'timestamp'],

            'status' => ['type' => 'key'],
            'base_module' => ['type' => 'key'],
            'sort' => ['type' => 'key'],
            'status_contract' => ['type' => 'key'],
            'cooperation' => ['type' => 'key'],
            'position' => ['type' => 'key'],
            'type' => ['type' => 'key'],
            'channel' => ['type' => 'key'],
            'status_pay' => ['type' => 'key'],

            'slide' => ['type' => 'imgtag'],
            'orderlist' => ['type' => 'change', 'formatView' => 'raw', 'width' => '50'],

            'operation' => ['type' => 'operation'],
		], $this->_fieldTemplatesExt());
	}

	public function _fieldTemplatesExt()
	{
		return [];
	}

	protected function getWebsiteSkuStr()
	{
		if (empty($this->websiteSkuData)) {
			return $this->website_sku_id;
		}
		$websiteGoodsData = $this->websiteSkuData->websiteGoodsData;
		$skuData = $this->websiteSkuData->skuData;
		$str = '';
		$str .= isset($websiteGoodsData['name']) ? $websiteGoodsData['name'] : '';
		$str .= isset($skuData['barcode']) ? ';--' . $skuData['barcode'] : '';
		$str .= ';--' . $skuData->skuStr;
		return $str;
	}

	protected function getSalemanName()
	{
		$saleman = $this->getPointModel('saleman')->getInfo($this->saleman_id);
		if (empty($saleman)) {
			return $this->saleman_id;
		}
		return $this->getPointName('teamwork', $saleman['teamwork_id']);
	}

	protected function _fieldFormatSearchElems($fields, $defaults = [])
	{
		$return = [];
		$datas = $this->_fieldSearchElems();
		foreach ($fields as $field) {
			$data = isset($datas[$field]) ? $datas[$field] : ['type' => 'common'];
			$data = isset($defaults[$field]) ? array_merge($data, $defaults[$field]) : $data;
			if (isset($data['alias'])) {
				$field = $data['alias'];
				unset($data['alias']);
			}
			$data['field'] = $field;
			$return[$field] = $data;
		}

		return $return;
	}

	protected function _fieldSearchElems()
	{
		return [
            'end_at' => ['type' => 'rangeTime'],
            'created_day' => ['type' => 'rangeTime', 'timestamp' => false],
            'created_at' => ['type' => 'rangeTime'],
            'pay_day' => ['type' => 'rangeTime', 'timestamp' => false],
		];
	}

	protected function _fieldFormatSearchDatas($type, $fields, $defaults = [], $splits = [])
	{
		$return = [];
		$datas = $this->_fieldSearchDatas($type);
		foreach ($fields as $field) {
			$data = isset($datas[$field]) ? $datas[$field] : [];
			$data = isset($defaults[$field]) ? array_merge($data, $defaults[$field]) : $data;
			if (!isset($data['method'])) {
			    $data['method'] =  $type == 'list' ? '_sKeyParam' : '_sTextParam';
			}
		    /*if (Yii::$app->controller->checkBackendPriv) {
				$data['priv'] = true;
			}*/
			$method = $data['method'];
			unset($data['method']);
			if (isset($data['alias'])) {
				$field = $data['alias'];
				unset($data['alias']);
			}
			$data['field'] = $field;
			if (isset($splits[$field])) {
				$this->_dealSplitElem($method, $splits[$field], $data, $return);
			} else {
			    $return[] = $this->$method($data);
			}
		}
		//print_r($return);exit();

		return $return;
	}

	public function _dealSplitElem($method, $splitInfo, $data, & $return)
	{
		$splitField = $splitInfo['field'];
		foreach ($splitInfo['wheres'] as $mark => $where) {
			$name = $this->getAttributeLabel($data['field']);
			$name .= '-' . $this->getKeyName($splitField, $mark);
			$data['name'] = $name;
			$data['where'] = $where;
			$return[] = $this->$method($data);
		}
	}

	protected function _fieldSearchDatas($type)
	{
		if ($type == 'list') {
    		return [
				'user_id' => ['method' => '_sPointParam', 'table' => 'user-merchant'],
				'skuv1' => ['method' => '_sPointParam', 'table' => 'attribute-value'],//, 'valueName' => 'value'],
				'skuv2' => ['method' => '_sPointParam', 'table' => 'attribute-value'],//, 'valueName' => 'value'],
                'account_id' => ['method' => '_sPointParam', 'table' => 'account-spread'],
                'scene_id' => ['method' => '_sPointParam', 'table' => 'scene'],
                'merchant_id' => ['method' => '_sPointParam', 'table' => 'merchant'],
                'supplier_id' => ['method' => '_sPointParam', 'table' => 'merchant', 'where' => ['sort' => 'supplier']],
                'website_id' => ['method' => '_sPointParam', 'table' => 'website'],
                'warehouse_id' => ['method' => '_sPointParam', 'table' => 'warehouse'],
                'role' => ['method' => '_sPointParam', 'table' => 'role', 'indexName' => 'code'],
                //'city_code' => ['method' => '_sPointParam', 'table' => 'region', 'indexName' => 'code', 'where' => ['status' => 2]],
                'city' => ['method' => '_sKeyParam', 'infos' => $this->getPointModel('company')->getRunInfos()],
                //'saleman_id' => ['method' => '_sPointParam', 'table' => 'saleman'],
    		];
		}
		return [
			'created_at' => ['method' => '_sStartParam'],
			'created_day' => ['method' => '_sStartParam'],
			'pay_day' => ['method' => '_sStartParam'],
            'end_at' => ['method' => '_sStartParam'],
			'field_hit' => ['method' => '_sHiddenParam'],
			'plan_id' => ['method' => '_sTextParam'],
	    ];
	}

	public function longStrHidden($view, $params)
	{
		$field = $params['field'];
		$lengthLimit = isset($params['length']) ? $params['length'] : 50;
		$length = strlen($this->$field);
		if ($length <= $lengthLimit) {
			return $this->$field;
		}

		return $this->_strHidden($this->$field, $lengthLimit);
	}

	protected function _strHidden($string, $length)
	{
		$str = Html::encode(mb_substr(urldecode($string), 0, $length, 'utf-8'));
        $str .= '<a href="javascript:void(0);" data-placement="top" data-toggle="popover" data-content="' . urldecode($string) . '" title=""><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>全部</a>';
		return $str;
	}

	public function getCityInfos()
	{
		return [
		];
	}
}
