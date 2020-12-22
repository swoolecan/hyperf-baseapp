<?php

namespace Swoolecan\Baseapp\Controllers\Traits;

use Yii;

trait TraitPriv
{
	protected function _dealPriv($type)
	{
		$privInfos = $this->privInfos;
		if ($privInfos === true) {
			return ;
		}

		$method = "_{$type}DealPriv";
		return $this->$method($privInfos);
	}

	public function _listinfoDealPriv($privInfos)
	{
		return ['id' => 'no'];
	}

	public function _updateDealPriv($model)
	{
		return false;
	}

	public function _deleteDealPriv()
	{
		return false;
	}

	public function _addDealPriv()
	{
		return false;
	}

	public function getIdentity()
	{
		return Yii::$app->controller->userInfo;
	}
}
