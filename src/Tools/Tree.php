<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Tools;

class Tree
{
    protected $parentField;
    protected $keyField;
    protected $infos;

    public function __construct($params)
    {
        $this->keyField = $params['keyField'];
        $this->parentField = $params['parentField'];
        $infos = $params['infos'];
        if (!isset($params['hasKey'])) {
            $datas = [];
            foreach ($infos as $info) {
                $datas[$info[$this->keyField]] = $info;
            }
            $infos = $datas;
        }
        $this->infos = $infos;
    }
    
    public function checkParent($parent)
    {
        return isset($this->infos[$parent]);
    }

    public function getFormatedInfos()
    {
        $infos = $this->find()->indexBy('code')->asArray()->all();
        $formatedInfos = $this->getTreeInfos($infos, 'code', 'parent_code', 'name', '');
        return $formatedInfos;
    }

    public function getSelectInfos()
    {
        $infos = $this->find()->select(['code', 'name', 'parent_code'])->indexBy('code')->asArray()->all();
        $datas = $this->getLevelDatas($infos, 'code', 'parent_code', 'name', '');
        return $datas;
    }

    public function getCateInfos()
    {
        $infos = $this->getInfos(['indexBy' => 'code', 'orderBy' => ['orderlist' => SORT_DESC]]);
        foreach ($infos as $key => & $info) {
            $info = $info->formatToArray();
        }
        return $infos;
    }

    public function getTreeInfos()
    {
        $cateInfos = $this->cateInfos;
        $datas = [];
        foreach ($cateInfos as $code => $info) {
            $datas[$info['parent_code']][$code] = $info;
        }
        $infos = $datas[''];
        foreach ($infos as $key => & $data) {
            $data['subInfos'] = isset($datas[$data['code']]) ? $datas[$data['code']] : [];
        }
        return $infos;
    }

    public function getSubDatas($parentCode, $point = false)
    {
        $infos = $this->getInfos(['where' => ['parent_code' => $parentCode], 'indexBy' => 'code']);
        if (empty($point)) {
            return $infos;
        }
        return ArrayHelper::map($infos, 'code', 'name');
    }

    public function getSiblingDatas($point = false)
    {
        return $this->getSubDatas($this->parent_code, $point);
    }

    public function getTreeInfosold($infos, $key, $parentKey, $name, $parentValue = 0)
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

    public function getTree($parentValue = '')
    {
        $datas = $this->getChildren($parentValue);
        foreach ($datas as $key => & $info) {
            $info['childrenInfos'] = $this->getTree($info[$this->keyField]);
        }
        return $datas;
    }

    public function getChildren($parentValue)
    {
        $childrenInfos = [];
        foreach ($this->infos as $key => $info) {
            if ($info[$this->parentField] == $parentValue) {
                $childrenInfos[$key] = $info;
            }
        }
        return $childrenInfos;
    }
}
