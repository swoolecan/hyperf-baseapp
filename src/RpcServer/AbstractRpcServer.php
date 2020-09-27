<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\RpcServer;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CachePut;
use Swoolecan\Baseapp\Helpers\ResourceContainer;

class AbstractRpcServer
{
    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @Inject                
     * @var ResourceContainer
     */
    protected $resource;

    /**
     * @Cacheable(prefix="", value="mc:#{app}:m:#{resource}:#{$keyField}:#{$key}")
     */
    public function getCacheData($resource, $key, $keyField = 'id')
    {
        $app = ucfirst($app);
        $class = "\App\RpcServer\\{$app}CacheRpcServer";
        $client = make($class);
        return $client->getCacheData($app, $resource, $key, $keyField);
    }
}
