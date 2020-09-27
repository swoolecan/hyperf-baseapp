<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

use Swoolecan\Baseapp\RpcClient\PassportRpcClient;
use Swoolecan\Baseapp\RpcClient\ThirdBaseRpcClient;

trait TraitData
{
    public function getPointValues($resourceCode)
    {
        return array_merge($this->model->getColumnElems(), $this->extAttributeNames());
    }

    public function getOutCacheData($app, $resource, $key, $keyField = 'id')
    {
        $app = ucfirst($app);
        $class = "\Swoolecan\Baseapp\RpcClien\\{$app}CacheRpcClient";
        $client = make($class);
        return $client->getCacheData($app, $resource, $key, $keyField);
        //$model = User::findFromCache($key);
    }

    public function getCacheData($resource, $key)
    {
        return $this->getModelObj($resource)->findFromCache($key);
    }

    public function getCacheDatas()
    {
        $models = User::findManyFromCache($ids);
    }
}
