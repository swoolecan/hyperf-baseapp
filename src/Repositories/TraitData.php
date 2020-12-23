<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;
use Hyperf\Cache\Annotation\CachePut;
use Hyperf\Cache\Annotation\Cacheable;

trait TraitData
{
    /*public function getPointValues($resourceCode)
    {
        return array_merge($this->model->getColumnElems(), $this->extAttributeNames());
    }*/

    public function getCacheOutData($app, $resource, $key, $keyField = 'id')
    {
        $app = ucfirst($app);
        $class = "\Swoolecan\Baseapp\RpcClient\\{$app}RpcClient";
        var_dump(class_exists($class));
        echo $class;
        $client = make($class);
        echo get_class($client); echo 'cccccccccccccccc';
        return $client->getCacheData($app, $resource, $key, $keyField);
    }

    public function getCacheData($resource, $key)
    {
        return $this->getModelObj($resource)->findFromCache($key);
    }

    public function getCacheDatas()
    {
        $models = User::findManyFromCache($ids);
    }

    public function getLevelDatas($parentValue, $level = 2)
    {
        $results = [];
    }

    public function cacheDatas($resource, $type = 'origin', $throw = true)
    {
        $model = $this->getModelObj($resource);
        $total = $model->count();
        if ($total > 5000) {
            if ($throw) {
                return $this->throwException('数据太多');
            }
            return false;
        }
        return $this->_cacheDatas($this->config->get('app_code'), $resource, $type);
    }

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

    /**
     * @Cacheable(prefix="fulltable-cache")
     */
    protected function _cacheDatas($app, $resource, $type)
    {
        $model = $this->getModelObj($resource);
        $keyField = $model->getKeyName();
        $infos = $model->all();
        if ($type == 'tree') {
            $parentField = $model->getParentField($keyField);
            $parentFirstValue = $model->getParentFirstValue($keyField);
            return $this->_formatTreeDatas($infos, $keyField, $parentField, $parentFirstValue);
        }
        $datas = [];
        foreach ($infos as $info) {
            $datas[$info[$keyField]] = $info;
        }
    
        return $datas;
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

    public function getPointKeyValues($where = [], $scene = 'keyvalue')
    {
        $datas = $this->findWhere($where);
        $collectionClass = $this->getCollectionClass();
        $collection = new $collectionClass($datas, $scene, $this);
        return $collection->toArray();
    }
}
