<?php

declare(strict_types=1);

namespace Swoolecan\baseapp\JsonRpcClient;

use Hyperf\RpcClient\AbstractServiceClient;

/**
 * 服务消费者
 */
class PassportBaseService extends AbstractServiceClient
{
    /**
     * 定义对应服务提供者的服务名称
     * @var string
     */
    protected $serviceName = 'PassportBaseService';

    /**
     * 定义对应服务提供者的服务协议
     * @var string
     */
    protected $protocol = 'jsonrpc-http';

    public function getResourceDatas(int $a, int $b): array
    {
        return $this->__request(__FUNCTION__, compact('a', 'b'));
    }

    public function getRouteDatas(): array
    {
        return $this->__request(__FUNCTION__);
    }
}
