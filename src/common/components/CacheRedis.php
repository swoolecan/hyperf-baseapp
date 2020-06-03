<?php

namespace common\components;
use yii\redis\Cache;

class CacheRedis extends Cache
{
    public function buildKey($key)
    {
        return $key;
    }
}
