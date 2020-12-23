<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\RpcClient;

use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CachePut;

/**
 * 服务消费者
 */
class ThirdRpcClient extends AbstractRpcClient
{
    /**
     * 定义对应服务提供者的服务名称
     * @var string
     */
    protected $serviceName = 'ThirdRpcServer';
}
