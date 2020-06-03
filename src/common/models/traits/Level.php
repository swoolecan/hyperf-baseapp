<?php

namespace common\models\traits;

use common\helpers\Tree;

trait Level
{
    public function getTreeInfos($infos, $key, $parentKey, $name, $parentValue = 0)
    {
        foreach ($infos as $id => $info) {
            $parentId = isset($infos[$info[$parentKey]]['id']) ? $infos[$info[$parentKey]]['id'] : 0;
            $parentNode = $parentId ? 'child-of-node-' . $parentId : '';
            $info['parentNode'] = $parentNode;
            $level = $this->getLevel($id, $infos, $parentKey);
            $info['level'] = $level;
            $infos[$id] = $info;
        }

        $tree = new Tree($infos, $key, $parentKey, $name);
        $formatedInfos = $tree->getTree($parentValue);

        return $formatedInfos;
    }

    protected function getLevelDatas($infos, $key, $parentKey, $name, $parentValue = 0)
    {
        $tree = new Tree($infos, $key, $parentKey, $name);
        $formatedInfos = $tree->getTree($parentValue);
        $datas = [];
        foreach ($formatedInfos as $code => $info) {
            $nameShow = str_replace('&nbsp;', '-', $info[$name]);
            $datas[$code] = $nameShow;
        }

        return $datas;
    }

    /**
     * Get the level of a multi level infos
     *
     * @return int
     */
    protected function getLevel($currentKey, $infos, $parentField = 'parentid', $level = 0)
    {
        foreach($infos as $key => $info){
            if ($currentKey == $key) {
                if (empty($info[$parentField])) {
                    return $level;
                }
                $level++;
                return $this->getLevel($info[$parentField], $infos, $parentField, $level);
            }
        }
    }

	public function getTreeDatas($infos, $key, $parentKey, $parent, $numberMax = 2000)
	{
		static $number = 1;
		$number++;
		if ($number > $numberMax) {
			return [];
		}
		$datas = [];
		foreach ($infos as $iKey => $iValue) {
			$info = $iValue->toArray();
			if ($info[$parentKey] == $parent) {
				$info['subInfos'] = $this->getTreeDatas($infos, $key, $parentKey, $info[$key], $numberMax);
				$datas[$info[$key]] = $info;
			}
		}
		return $datas;
	}

    public function getSubInfos($parentField = 'parent_code', $field = 'code')
    {
		static $datas;
		if (!isset($datas[$this->$field])) {
			$datas[$this->$field] = (array) $this->getInfos(['where' => [$parentField => $this->$field], 'indexBy' => $field]);
		}
		return $datas[$this->$field];
    }

    public function getParentInfo($parentField = 'parent_code', $field = 'code')
    {
		static $datas;
		if (!isset($datas[$this->$parentField])) {
			$datas[$this->$parentField] = $this->getInfo($this->$parentField, $field);
		}
		return $datas[$this->$parentField];
    }

    public function formatSubCodes($codes)
    {
        if (empty($codes)) {
            return null;
        }
        $codes = (array) $codes;
        $datas = $codes;
        foreach ($codes as $code) {
            $info = $this->getInfo($code, 'code');
            $subInfos = $info->getSubInfos();
            $datas = array_merge($datas, array_keys($subInfos));
        }
        return array_filter(array_unique($datas));
    }
}
