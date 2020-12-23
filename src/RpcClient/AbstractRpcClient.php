<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\RpcClient;

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
     */
    public function getCacheData($app, $resource, $key, $keyField = 'id')
    {
     //* @Cacheable(prefix="", value="mc:#{app}:m:#{resource}:#{$keyField}:#{$key}")
        $data = [
            'resource' => $resource,
            'key' => $key,
            'keyField' => $keyField
        ];
        return $this->__request(__FUNCTION__, $data);
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
