<?php

namespace common\models\traits;

use Yii;
use common\components\Pagination;

trait ActiveData
{
	public function getInfosByPage($params = [])
	{
		$infosObj = $this->getInfosObj($params);
		$pageSize = isset($params['pageSize']) ? $params['pageSize'] : 20;
        $pagePreStr = isset($params['pagePreStr']) ? $params['pagePreStr'] : '';
        $noHost = isset($params['noHost']) ? $params['noHost'] : '';
		$pages = new Pagination(['totalCount' => $infosObj->count(), 'pageSize' => $pageSize, 'defaultPageSize' => $pageSize, 'pagePreStr' => $pagePreStr, 'noHost' => $noHost]);

		$params['offset'] = $pages->offset;
		$params['limit'] = $pages->limit;
		$infos = $this->getInfos($params, $infosObj);

		$return = ['infos' => $infos, 'pages' => $pages];
		return $return;
	}

	public function getInfos($params = [], $infosObj = null)
	{
		$infosObj = is_null($infosObj) ? $this->getInfosObj($params) : $infosObj;
		$offset = isset($params['offset']) ? $params['offset'] : 0;
		$limit = isset($params['limit']) ? $params['limit'] : 1000;
		$infos = $infosObj->offset($offset)->limit($limit)->all();
		//$sql = $infosObj->createCommand()->getRawSql(); file_put_contents('/tmp/sql.sql', $sql, FILE_APPEND);
		return $infos;
	}

	protected function getInfosObj($params = [])
	{
		$where = isset($params['where']) ? $params['where'] : [];
		if (isset($params['havePriv'])) {
			$wherePriv = $this->dealPriv('listinfo');
			$where = $wherePriv === true ? $where : ['and', $wherePriv, $where];
		}
		/*if (isset($params['haveOwner'])) {
			$whereOwner = $this->dealOwnerPriv('listinfo');
			$where = $whereOwner === true ? $where : ['and', $whereOwner, $where];
		}*/
		$orderBy = isset($params['orderBy']) ? $params['orderBy'] : ['id' => SORT_DESC];
		$groupBy = isset($params['groupBy']) && !empty($params['groupBy']) ? $params['groupBy'] : null;
		$asArray = isset($params['asArray']) && !empty($params['asArray']) ? true : false;

        $selectStr = isset($params['select']) ? $params['select'] : $this->_getSelect();
        $infosObj = $this->find()->select($selectStr)->where($where);
		if (isset($params['indexBy'])) {
			$infosObj->indexBy($params['indexBy']);
		}
        if (isset($params['andWhere'])) {
            $infosObj = $infosObj->andWhere($params['andWhere']);
        }
		if (!empty($asArray)) {
			$infosObj->asArray();
		}

		$infosObj = !empty($orderBy) ? $infosObj->orderBy($orderBy) : $infosObj;
		$infosObj = !empty($groupBy) ? $infosObj->groupBy($groupBy) : $infosObj;
		return $infosObj;
	}

	public function getInfosCount($params = [])
	{
		//$sql = $this->getInfosObj($params)->createCommand()->getRawSql(); echo $sql; //file_put_contents('/tmp/sql.sql', $sql, FILE_APPEND);
		return $this->getInfosObj($params)->count();
	}

	public function getInfoModel($param, $keyField = 'id')
	{
		$info = $this->getInfo($param, $keyField);
		return empty($info) ? $this : $info;
	}

	public function getInfo($param, $keyField = 'id')
	{
        $orderBy = false;
        if (is_array($param)) {
            $where = isset($param['where']) ? $param['where'] : $param;
		    $orderBy = isset($param['orderBy']) ? $param['orderBy'] : false;
        } else {
            $where = [$keyField => $param];
        }

        if (!empty($orderBy)) {
		    $info = $this->find()->where($where)->orderBy($orderBy)->limit(1)->one();
        } else {
		    $info = $this->find()->where($where)->limit(1)->one();
        }

        //\Yii::$app->cacheRedis->set($key, $info);
		return $info;
	}

    protected function _getSelect()
    {
        return '*';
    }

	public function getCommonDatas($limit, $extWhere = [], $orderBy = null)
	{
		$params['limit'] = $limit;
		$baseWhere = ['status' => 1];
        $params['where'] = array_merge($baseWhere, $extWhere);
        $params['orderBy'] = !empty($orderBy) ? $orderBy : ['orderlist' => SORT_DESC];
		return $this->getInfos($params);
	}

	public function getInfosKeys($params, $pointKey = 'id')
	{
		$params['indexBy'] = $pointKey;
		$infos = (array) $this->getInfos($params);
		return array_keys($infos);
	}

	public function getInfoNum($params)
	{
		return $this->find()->where($params['where'])->count();
	}

	public function getSkuStr()
	{
		$skuData = $this->skuData;
		if (empty($skuData) || empty($skuData['id'])) {
			return $this->sku_id;
		}
		$str = $this->getPointName('attribute-value', $skuData['skuv1']) . '-' . $this->getPointName('attribute-value', $skuData['skuv2']);
		return $str;
	}

	public function formatToArray()
	{
		$restArray = $this->restArray();
		if (empty($restArray)) {
		    return $this->toArray();
		}
		$return = [];
		if (isset($restArray['baseInfo'])) {
			$base = $restArray['baseInfo'];
			$baseData = $this->toArray();
			$onlyShows = isset($base['onlyShows']) ? $base['onlyShows'] : [];
			$noShows = isset($base['noShows']) ? $base['noShows'] : [];
			foreach ($baseData as $key => $vlaue) {
				if (in_array($key, $noShows) || (!empty($onlyShows) && !in_array($key, $onlyShows))) {
					unset($baseData[$key]);
				}
			}
		    $return['baseInfo'] = $baseData;
		}

		if (isset($restArray['extFields'])) {
			foreach ($restArray['extFields'] as $key => $fields) {
				foreach ($fields as $field) {
					$return['baseInfo'][$field] = $this->$key->$field;
				}
			}
		}
		if (isset($restArray['relateData'])) {
			foreach ($restArray['relateData'] as $key => $fields) {
				foreach ($fields as $field) {
					$return['relateData'][$key][$field] = $this->$key->$field;
				}
			}
		}
		return $return;
	}

	public function restArray()
	{
		return false;
	}
}
