<?php

declare(strict_types=1);

namespace Swoolecan\baseapp\RpcClient;

use Hyperf\RpcClient\AbstractServiceClient;

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
}
