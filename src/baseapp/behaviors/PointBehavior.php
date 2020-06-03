<?php

namespace baseapp\behaviors;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;

class PointBehavior extends Behavior
{
    public function getPointName($code, $where, $nameField = 'name')
    {
        $info = $this->getPointInfo($code, $where);
        $default = is_scalar($where) ? $where : array_pop($where);
        return empty($info) ? $default : $info[$nameField];
    }

    public function getPointInfo($code, $where)
    {
        $where = is_array($where) ? $where : ['id' => $where];
        return $this->getPointModel($code)->find()->where($where)->one();
    }

    public function getPointInfos($code, $params = [])
    {
        $indexName = isset($params['indexName']) ? $params['indexName'] : 'id';
        $valueName = isset($params['valueName']) ? $params['valueName'] : 'name';
        $params['select'] = "{$indexName}, {$valueName}";
        $params['indexBy'] = $indexName;
        
        $infos = ArrayHelper::map($this->getPointAll($code, $params), $indexName, $valueName);
        return $infos;
    }

    public function getPointDatas($code, $params = [], $forceNew = false)
    {
        static $datas;
		$keySuffix = (array) $params;
		sort($keySuffix);
		//$code = $this->owner->getShortTable();
		
        $key = md5($code . serialize($keySuffix));

		if ($this->owner->hasProperty('status')) {
			if (!isset($params['where'])) {
				$params['where'] = ['status' => 1];
			} else {
				$params['where'] = !isset($params['where']['status']) ? array_merge($params['where'], ['status' => [1]]) : $params['where'];
			}
		}
		if (!isset($params['orderBy']) && $this->owner->hasProperty('orderlist')) {
            $params['orderBy'] = ['orderlist' => SORT_DESC];
		}

        if (!isset($datas[$key]) || $forceNew) {
            $datas[$key] = $this->getPointAll($code, $params);
        }
        return $datas[$key];
    }

    public function getPointAll($code, $params = [])
    {
        $where = isset($params['where']) ? $params['where'] : null;
        $indexBy = isset($params['indexBy']) ? $params['indexBy'] : 'id';
        $select = isset($params['select']) ? $params['select'] : '*';
        $limit = isset($params['limit']) ? $params['limit'] : 1000;
        $orderBy = isset($params['orderBy']) ? $params['orderBy'] : ['id' => SORT_DESC];
        $limit = min($limit, 2000);
		$paramsNew = [
			'where' => $where,
			'select' => $select,
			'indexBy' => $indexBy,
		    'orderBy' => $orderBy,
			'limit' => $limit,
		];
		if (isset($params['haveOwner'])) {
			$paramsNew['haveOwner'] = true;
		}
		if (isset($params['havePriv'])) {
			$paramsNew['havePriv'] = true;
		}
        $infos = $this->getPointModel($code)->getInfos($paramsNew);
        return $infos;
    }

    public function getPointModel($code, $forceNew = false, $data = [])
    {
        static $models;
        if (isset($models[$code]) && empty($forceNew)) {
            return $models[$code];
        }

        $class = $this->getPointClass($code);
        $model = new $class($data);
        $models[$code] = $model;
        return $models[$code];
    }

    protected function getPointClass($class)
    {
		$classes = \common\helpers\InitFormat::runtimeParams('model');
		if (!isset($classes[$class])) {
			//exit($class);
		}
        return $classes[$class];
    }
}
