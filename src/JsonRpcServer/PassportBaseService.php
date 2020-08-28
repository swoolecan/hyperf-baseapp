<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\JsonRpcServer;

use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="PassportBaseService", protocol="jsonrpc-http", server="jsonrpc-http", publishTo="consul")
 */
class PassportBaseService
{
    public function getResourceDatas(): array
    {
        return ['a' => 'b'];
    }

    public function getRouteDatas(): array
    {
        return ['a' => 'b'];
    }
}
