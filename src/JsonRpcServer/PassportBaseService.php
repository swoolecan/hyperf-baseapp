<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\JsonRpcServer;

use Hyperf\RpcServer\Annotation\RpcService;
use App\Services\UserPermissionService;

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

    public function getUserById($id): array
    {
        $userPermission = make(UserPermissionService::class);
        $user = $userPermission->getUserById($id);
        if (empty($user)) {
            return ['code' => 400, 'message' => 'Token获取用户失败'];
        }

        return ['code' => '200', 'message' => 'OK', 'data' => $user];
    }

    public function checkPermission($token, $routeCode): array
    {
        return ['code' => 200, 'message' => 'OK'];
    }
}
