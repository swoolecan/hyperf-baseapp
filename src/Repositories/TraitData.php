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
        $client = make($class);
        return $client->getCacheData($app, $resource, $key, $keyField);
    }

    public function getSingleAttachmentData($app, $params)
    {
        $currentAppCode = config('app_code');
        if ($currentAppCode == 'passport') {
            return $this->getCacheData('attachmentInfo', $params);
        }

        return $this->getCacheOutData('passport', 'attachmentInfo', $params);
    }

    public function getAttachmentData($app, $params)
    {
        $currentAppCode = config('app_code');
        if ($currentAppCode == 'passport') {
            return $this->getCacheData('attachmentInfo', $params);
        }
    }

    public function getCacheData($resource, $key)
    {
        return $this->getModelObj($resource)->findCacheData($key);
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

    public function getPointKeyValues($where = [], $scene = 'keyvalue')
    {
        $datas = $this->findWhere($where);
        $collectionClass = $this->getCollectionClass();
        $collection = new $collectionClass($datas, $scene, $this);
        return $collection->toArray();
    }
}
