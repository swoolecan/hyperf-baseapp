<?php

declare(strict_types=1);

namespace Swoolecan\baseapp\RpcClient;

use Hyperf\RpcClient\AbstractServiceClient;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CachePut;

/**
 * 服务消费者
 */
class AbstractRpcClient extends AbstractServiceClient
{
    /**
     * 定义对应服务提供者的服务协议
     * @var string
     */
    protected $protocol = 'jsonrpc-http';

    /**
     * @Cacheable(prefix="", value="mc:#{app}:m:#{resource}:#{$keyField}:#{$key}")
     */
    public function getCacheData($app, $resource, $key, $keyField = 'id')
    {
        $app = ucfirst($app);
        $class = "\App\RpcServer\\{$app}CacheRpcServer";
        $client = make($class);
        return $client->getCacheData($app, $resource, $key, $keyField);
    }

    /**
     * @Cacheable(prefix="", value="#{app}.#{resource}.#{$key}")
     */
    public function getCacheDatas($app, $resource, $key = 'routes')
    {
        $passportBase = make(PassportBaseService::class);
        $routes = $passportBase->getRouteDatas();
    }
}
