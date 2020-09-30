<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;
use Hyperf\Cache\Annotation\CachePut;

trait TraitData
{
    public function getPointValues($resourceCode)
    {
        return array_merge($this->model->getColumnElems(), $this->extAttributeNames());
    }

    public function getCacheOutData($app, $resource, $key, $keyField = 'id')
    {
        $app = ucfirst($app);
        $class = "\Swoolecan\Baseapp\RpcClient\\{$app}RpcClient";
        $client = make($class);
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

    public function cacheDatas($resource, $type = 'origin')
    {
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
     * @CachePut(prefix="fulltable-cache")
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

    public function _formatTreeDatas($infos, $key, $parentKey, $parent)
    { 
        echo count($infos) . '===';
        if (count($infos) > 5000) {
            return $this->throwException('数据太多');
        }
        $datas = [];          
        foreach ($infos as $iKey => $iValue) { 
            $info = $iValue->toArray();    
            if ($info[$parentKey] == $parent) {
                unset($infos[$iKey]);
                $info['subInfos'] = $this->_formatTreeDatas($infos, $key, $parentKey, $info[$key]);
                $datas[$info[$key]] = $info;   
            }
        }
        return $datas;        
    }
}
