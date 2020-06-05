<?php

namespace common\models\traits;

use Yii;

trait Priv
{
	public function dealPriv($type, $typeExt = '')
	{
		$ignorePriv = Yii::$app->controller->ignorePriv;
		$userPrivs = Yii::$app->controller->userPrivs;
		$rolePrivs = Yii::$app->controller->rolePrivs;
		if ($ignorePriv === true || $userPrivs === true || $rolePrivs === true) {
			return true;
		}

		$privs = [];
		foreach ((array) $rolePrivs as $mId => $rolePriv) {
			if (!isset($rolePriv['resources'])) {
				continue;
			}
			if (isset($rolePriv['resources'][$this->modelCode]) && isset($rolePriv['resources'][$this->modelCode][$type])) {
				$privs[$mId] = $rolePriv['resources'][$this->modelCode][$type];
			}
		}
		$mIds = $this->formatMerchantIds($userPrivs, $privs);
		if (in_array($type, ['view', 'update', 'delete']) || ($type == 'add' && $typeExt == 'submit')) {
		    $privMerchantId = $this->privMerchantId($mIds);
		    if ($privMerchantId !== true && !in_array($privMerchantId, $mIds)) {
                $this->addError('id', '商家权限有误');
			    return false;
		    }
		}
		if ($typeExt == 'submit') {
			$submitResult = $this->_submitDealPriv($userPrivs, $rolePrivs, $mIds);
			if (empty($submitResult)) {
				return false;
			}
		}
		$method = "_{$type}DealPriv";
		if (!method_exists($this, $method)) {
			$method = '_viewDealPriv';
		}

		return $this->$method($userPrivs, $privs, $mIds);
	}

	public function _submitDealPriv($userPrivs, $rolePrivs, $mIds)
	{
		return true;
	}

	public function _listinfoDealPriv($userPrivs, $privs, $mIds)
	{
		if (method_exists($this, '_privWrap')) {
			return $this->_privWrap($mIds, $userPrivs);
		}
		$sIds = $userPrivs['supplierIds'];
		if ($this->hasProperty('supplier_id', false) && $this->hasProperty('merchant_id', false)) {
			return ['or', ['supplier_id' => $sIds], ['merchant_id' => $mIds]];
		} elseif ($this->hasProperty('supplier_id', false)) {
			return ['supplier_id' => $mIds];
		} elseif ($this->hasProperty('merchant_id', false)) {
			return ['merchant_id' => $mIds];
		}
		return ['id' => 'no'];
	}

	public function _updateDealPriv()
	{
		return true;
	}

	public function _deleteDealPriv()
	{
		return true;
	}

	public function _addDealPriv()
	{
		return true;
	}

	public function _viewDealPriv($userPrivs, $privs)
	{
		return true;
		$privFields = [];
		foreach ($privs as $mId => $priv) {
			$rtype = $this->privMerchantRtype($mId, $privMerchantId, $userPrivs);
			if (empty($rtype) || (!$priv['strict'] != 'all' && !in_array($priv['strict'], $rtype))) {
				continue;
			}
			$privFields = $this->privFieldsDeal($privFields, $priv);
		}
		if (empty($privFields)) {
		    return false;
		}
		$this->privFields = $privFields;
		return true;
	}

	protected function privMerchantRtype($targetId, $id, $userPrivs)
	{
		if ($targetId == $id) {
			return ['self'];
		}
		$wholesalerIds = isset($userPrivs['wholesalerIds'][$targetId]) ? $userPrivs['wholesalerIds'][$targetId] : [];
		$clientIds = isset($userPrivs['clientIds'][$targetId]) ? $userPrivs['clientIds'][$targetId] : [];
		if (in_array($id, $wholesalerIds) && in_array($id, $clientIds)) {
			return ['supplier', 'client'];
		}
		if (in_array($id, $wholesalerIds)) {
			return ['supplier'];
		}
		if (in_array($id, $clientIds)) {
			return ['client'];
		}
	}

	protected function privMerchantId($mIds)
	{
		if (method_exists($this, '_privMerchantId')) {
			return $this->_privMerchantId($mIds);
		}
		
		if ($this->hasProperty('merchant_id', false)) {
			return $this->merchant_id;
		}
		return 0;
	}

	protected function privFieldsDeal($privFields, $priv)
	{
		if (isset($privFields['haveFullPriv'])) {
			return $privFields;
		}
		if (empty($priv['field_only']) && empty($priv['field_except'])) {
			$privFields['haveFullPriv'] = true;
			return $privFields;
		}
		if (!empty($priv['field_only'])) {
			$only = isset($privFields['only']) ? $privFields['only'] : [];
			$privFields['only'] = array_merge($only, $priv['field_only']);
		}
		if (!empty($priv['field_except'])) {
			$except = isset($privFields['except']) ? $privFields['except'] : $priv['field_except'];
			$privFields['except'] = array_intersect($except, $priv['field_except']);
		}

		return $privFields;
	}

	public function getIdentity()
	{
		return Yii::$app->controller->userInfo;
	}

	protected function formatMerchantIds($userPrivs, $privs)
	{
		$mIds = $userPrivs['merchantIds'];
		foreach ($privs as $mId => $priv) {
			$wholesalerMids = isset($userPrivs['wholesalerIds'][$mId]) ? $userPrivs['wholesalerIds'][$mId] : [];
			$clientMids = isset($userPrivs['clientIds'][$mId]) ? $userPrivs['clientIds'][$mId] : [];
			//print_r($clientMids);exit();
			switch ($priv['strict']) {
			case 'self':
				break;
			case 'wholesaler':
				$mIds = array_merge($mIds, $wholesalerMids);
				break;
			case 'client':
				$mIds = array_merge($mIds, $clientMids);
				break;
			case 'all':
				$mIds = array_merge($mIds, $wholesalerMids);
				$mIds = array_merge($mIds, $clientMids);
				break;
			}
		}
		$mIds = array_filter((array) $mIds);
		//print_r($mIds);exit();
		return $mIds;
	}
}
