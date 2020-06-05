<?php

namespace common\models\traits;

use Yii;

trait RelateData
{
	// passport
	public function getRegionProvinceData() { return $this->_getPointModelData('region', $this->province_code, 'code'); }
	public function getRegionCityData() { return $this->_getPointModelData('region', $this->city_code, 'code'); }
	public function getRegionCountyData() { return $this->_getPointModelData('region', $this->county_code, 'code'); }
	public function getMenuData() { return $this->_getPointModelData('menu', $this->menu_code, 'code'); }
	// shop
	public function getCategoryShopData() { return $this->_getPointModelData('category-shop', $this->category_code, 'code'); }
	public function getBatchData() { return $this->_getPointModelData('batch', $this->batch_code, 'code'); }
	public function getSceneGoodsData() { return $this->_getPointModelData('scene-goods', $this->scene_goods_id); }
	public function getWebsiteGoodsData() { return $this->_getPointModelData('website-goods', $this->website_goods_id); }
	public function getWebsiteSkuData() { return $this->_getPointModelData('website-sku', $this->website_sku_id); }
	public function getGoodsData() { return $this->_getPointModelData('goods', $this->goods_id); }
	public function getSkuData() { return $this->_getPointModelData('goods-sku', $this->sku_id); }
	public function getWebsiteData() { return $this->_getPointModelData('website', $this->website_id); }
	public function getWarehouseTargetData() { return $this->_getPointModelData('warehouse', $this->warehouse_id_target); }
	public function getWarehouseData() { return $this->_getPointModelData('warehouse', $this->warehouse_id); }
	public function getAttributeData() { return $this->_getPointModelData('attribute', $this->attribute_id); }

    // paytrade
	public function getOrderInfoData() { return $this->_getPointModelData('order-info', $this->orderid, 'orderid'); }
	public function getRealnameData() { return $this->_getPointModelData('realname', $this->realname_id); }

    // business
	public function getBusinessBaseData() { return $this->_getPointModelData('business-base', $this->orderid, 'orderid'); }
	public function getBusinessInfoData() { return $this->_getPointModelData('business-info', $this->orderid, 'orderid'); }
	public function getBusinessExpressData() { return $this->_getPointModelData('business-express', $this->orderid_express, 'orderid_express'); }
	public function getSceneData() { return $this->_getPointModelData('scene', $this->scene_id); }
	public function getVisitorData() { return $this->_getPointModelData('visitor', $this->visitor_id); }

    // other
	public function getSpiderinfoData() { return $this->_getPointModelData('spiderinfo', $this->spiderinfo_id); }

    public function getBusinessExrecordDatas($rField = 'orderid_express', $params = []) { return $this->_relateDatas('business-exrecord', $rField, $params); }


    public function getMemberInfo() { return $this->_getPointModelData('user', $this->user_id); }

    // merchant
	public function getUserData() { return $this->_getPointModelData('user-merchant', $this->user_id); }
	public function getServiceData() { return $this->_getPointModelData('service', $this->service_id); }
	public function getMerchantData() { return $this->_getPointModelData('merchant', $this->merchant_id); }
	public function getSupplierData() { return $this->_getPointModelData('merchant', $this->supplier_id); }
	public function getSalemanData() { return $this->_getPointModelData('saleman', $this->saleman_id); }
	public function getTeamworkData() { return $this->_getPointModelData('teamwork', $this->teamwork_id); }

    public function getSalemanInfos($field = 'supplier_id') 
	{
		return $this->getPointModel('saleman')->getKeyParams(['where' => ['merchant_id' => $this->$field, 'status' => 'service']]);
	}

    public function getServiceInfos($field = 'supplier_id') 
	{
		return $this->getPointModel('service')->getKeyParams(['where' => ['merchant_id' => $this->$field, 'status' => 'service']]);
	}

    public function getTeamworkInfos($field = 'merchant_id') 
	{
		return $this->getPointInfos('teamwork', ['where' => ['merchant_id' => $this->$field, 'status' => 'confirm']]);
	}

    // spread
    public function getConversionData() { return $this->_getPointModelData('conversion', $this->conversion_id); }

    // proom
    public function getInstitutionData() { return $this->_getPointModelData('institution-proom', $this->institution_id); }

	public function getUserIdsByKeyword($keyword)
	{
		return $this->getElemIdsByKeyword($keyword, 'user-merchant', ['name', 'mobile', 'nickname']);
	}

	public function getElemIdsByKeyword($keyword, $elemCode, $pointFields = 'name')
	{
		if (empty($keyword)) {
			return null;
		}
		if (is_array($pointFields)) {
			$where = ['or'];
			foreach ($pointFields as $pField) {
				$where[] = "{$pField} LIKE '%{$keyword}%'";
			}
		} else {
			$where = ['like', $pointFields, $keyword];
		}
		$params = [
			'where' => $where,
			'select' => 'id',
			'limit' => 2000,
			'indexBy' => 'id',
		];
		$infos = (array) $this->getPointModel($elemCode)->getInfos($params);
		return array_keys($infos);
	}

	public function getIdsByName($keyword, $fields = ['name'], $fuzzy = true)
	{
		if (empty($keyword)) {
			return null;
		}
		$where = '';
		$fields = (array) $fields;
		foreach ($fields as $field) {
			$where = $fuzzy ? ['or', ['like', $field, $keyword], $where] : ['or', ['=', $field, $keyword], $where];
		}
		$params = ['where' => $where, 'indexBy' => 'id'];
		$infos = $this->getInfos($params);
		$result = empty($infos) ? [] : array_keys($infos);
		return $result;
	}

	public function getMerchantIdsByElem($elemCode, $elemIds, $pointField = 'merchant_id')
	{
		$params = [
			'where' => ['id' => $elemIds], 
			'indexBy' => $pointField,
			'select' => 'merchant_id',
		];
		$datas = (array) $this->getPointModel($elemCode)->getInfos($params);
		return array_keys($datas);
	}

	public function getElemIdsByMerchant($elemCode, $merchantIds, $pointField = 'merchant_id')
	{
		if (!is_array($merchantIds)) {
			$merchantIds = $this->getElemIdsByKeyword($merchantIds);
		}
		$params = [
			'where' => [$pointField => $merchantIds], 
			'indexBy' => 'id',
			'select' => 'id',
		];
		$datas = (array) $this->getPointModel($elemCode)->getInfos($params);
		return array_keys($datas);
	}

	public function mergeSearchElems($elems, $elemExt)
	{
		if (is_null($elems)) {
			return $elemExt;
		}
		if (is_null($elemExt)) {
			return $elems;
		}

		return array_intersect($elems, $elemExt);
	}

	public function getRegionInfos($parentCode = '', $type = '')
	{
		if (empty($parentCode) && in_array($type, ['city', 'county'])) {
			return [];
		}
		return $this->getPointInfos('region', ['indexName' => 'code', 'where' => ['parent_code' => $parentCode]]);
	}

	public function _relateDatas($code, $rField, $params)
	{
		$where = [$rField => $this->$rField];
		$params['where'] = isset($params['where']) ? ['and', $params['where'], $where] : $where;
		return $this->getPointModel($code)->getInfos($params);
	}

	public function _getPointModeldata($code, $value, $field = 'id')
	{
		$datas = Yii::$app->params['pointModelDatas'];
		if (isset($datas[$code]) && isset($datas[$code][$value])) {
			return $datas[$code][$value];
		}
		if (!isset($datas[$code])) {
			$datas[$code] = [];
		}
		$datas[$code][$value] = $this->getPointModel($code)->getInfoModel($value, $field);
		Yii::$app->params['pointModelDatas'] = $datas;
		return $datas[$code][$value];
	}

	public function getIdsByTag($sort, $tag, $type, $fuzzy = true)
	{
		if (empty($tag)) {
			return null;
		}
		$where = $fuzzy ? ['like', 'name', $tag] : ['name' => $tag];
		$infos = $this->getPointModel('tag-' . $sort)->getInfos(['where' => $where, 'select' => 'code', 'indexBy' => 'code']);
		$codes = empty($infos) ? [] : array_keys($infos);
		return $this->getIdsByTagCode($sort, $codes, $type);
	}

	public function getIdsByTagCode($sort, $tagCodes, $type)
	{
		$datas = $this->getPointModel('tag-info-' . $sort)->getInfos(['where' => ['info_type' => $type, 'tag_code' => $tagCodes], 'select' => 'info_id', 'indexBy' => 'info_id']);
		return empty($datas) ? null : array_keys($datas);
	}

	public function getInfoTags($id, $sort, $type)
	{
		$tagInfos = $this->getPointModel('tag-info-' . $sort)->getInfos(['where' => ['info_type' => $type, 'info_id' => $id], 'indexBy' => 'tag_code']);
		$codes = empty($tagInfos) ? [] : array_keys($tagInfos);
		return $this->getPointInfos('tag-' . $sort, ['where' => ['code' => $codes], 'indexName' => 'code']);
	}
}
