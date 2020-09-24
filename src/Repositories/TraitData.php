<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

trait TraitData
{
    public function getPointValues($resourceCode)
    {
        return array_merge($this->model->getColumnElems(), $this->extAttributeNames());
    }

    public function getCacheDatas()
    {
        $models = User::findManyFromCache($ids);
    }

    public function getCacheData($key, $keyField = 'id')
    {
        $model = User::findFromCache($key);
    }
}
