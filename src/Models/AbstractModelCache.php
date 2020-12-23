<?php

declare(strict_types = 1);
/**
 * This file is an abstract controller for hyperf
 *
 * @link     http://http://home.canliang.wang/
 * @document http://wiki.canliang.wang/
 * @contact  iamwangcan@gmail.com
 * @license  https://github.com/swoolecan/hyperf-baseapp/blob/master/LICENSE.md
 */

namespace Swoolecan\Baseapp\Models;

use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

abstract class AbstractModelCache extends AbstractModel implements CacheableInterface
{
    use Cacheable;

    public function hasOneCache($related, $foreignKey, $localKey)
    {
        $instance = $this->newRelatedInstance($related);
        $model = $instance->findFromCache($this->$localKey);
        return $model;
    }

    public function findCacheData($key)
    {
        return $this->findFromCache($key);
    }
}
