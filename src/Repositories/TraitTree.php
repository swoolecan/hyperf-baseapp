<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

use Hyperf\Cache\Annotation\CachePut;
use Hyperf\Cache\Annotation\Cacheable;

trait TraitTree
{
    public function cacheBaseTreeDatas($resource, $level = 2)
    {
        $infos = $this->_cacheDatas($this->config->get('app_code'), $resource, 'tree');
        foreach ($infos as & $info) {
            if ($level == 1) {
                unset($info['subInfos']);
                continue;
            }
            foreach ($info['subInfos'] as & $sInfo) {
                if ($level == 2) {
                    unset($sInfo['subInfos']);
                    continue;
                }
                foreach ($sInfo['subInfos'] as & $ssInfo) {
                    if ($level == 2) {
                        unset($ssInfo['subInfos']);
                        continue;
                    }
                }
            }
        }
        return $infos;
    }

    public function _formatTreeDatas($infos, $key, $parentKey, $parent, $forceArray = false)
    { 
        $datas = [];          
        foreach ($infos as $iKey => $info) { 
            //$info = $iValue->toArray();    
            if ($info[$parentKey] == $parent) {
                unset($infos[$iKey]);
                $formatInfo = $this->getFormatShowFields('list', $info);
                //$formatInfo = $info->toArray();
                $formatInfo['subInfos'] = $this->_formatTreeDatas($infos, $key, $parentKey, $info[$key], $forceArray);
                //$formatInfo['hasChildren'] = count($formatInfo['subInfos']) > 0 ? true : false;
                $keyField = $info->getKeyName();
                $formatInfo['keyField'] = $info->$keyField;
                if ($forceArray) {
                    $datas[] = $formatInfo;
                } else {
                    $datas[$info[$key]] = $formatInfo;   
                }
            }
        }
        return $datas;        
    }

    public function getTreeInfos()
    {
        $model = $this->model;
        $total = $model->count();
        if ($total > 5000) {
            return $this->throwException('数据太多');
        }
        $infos = $this->all();
        $keyField = $model->getKeyName();
        $parentField = $model->getParentField($keyField);
        $parentFirstValue = $model->getParentFirstValue($keyField);
        $infos = $this->_formatTreeDatas($infos, $keyField, $parentField, $parentFirstValue, true);
        $addFormFields = $this->getFormatFormFields('add');
        $updateFormFields = $this->getFormatFormFields('update');
        return [
            'data' => $infos,
            'fieldNames' => $this->getAttributeNames('list'),
            'addFormFields' => $addFormFields ? $addFormFields : (object)[],
            'updateFormFields' => $updateFormFields ? $updateFormFields : (object)[],
        ];
    }

    public function getParentChains($info)
    {
        $model = $this->getModel();
        $keyField = $model->getKeyName();
        $parentField = $model->getParentField($keyField);
        $parentFirstValue = $model->getParentFirstValue($keyField);

        $parents = [];
        $currentInfo = $info;
        $parentValue = $info[$parentField];
        while ($parentValue != $parentFirstValue) {
            $parent = $model->find($parentValue);
            $parentValue = $parentFirstValue;
            if (!empty($parent)) {
                $parents[] = $parent;
                $parentValue = $parent[$parentField];
            }
        }
        $parents = array_reverse($parents);
        return $parents;
    }
}
