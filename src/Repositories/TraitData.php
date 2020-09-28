<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

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
}
