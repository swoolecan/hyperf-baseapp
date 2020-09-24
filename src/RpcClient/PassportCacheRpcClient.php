<?php

declare(strict_types=1);

namespace Swoolecan\baseapp\RpcClient;

use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CachePut;

/**
 * 服务消费者
 */
class PassportCacheRpcClient extends AbstractRpcClient
{
    /**
     * 定义对应服务提供者的服务名称
     * @var string
     */
    protected $serviceName = 'PassportRpcClient';

    public function getResourceDatas(int $a, int $b): array
    {
        return $this->__request(__FUNCTION__, compact('a', 'b'));
    }

    public function getRouteDatas(): array
    {
        $p = 'a';
        //return require('/data/htmlwww/docker/container/passport/config/autoload/routes.php');
        return $this->__request(__FUNCTION__, ['a' => 'b']);
    }

    public function checkPermission($token): array
    {
        $p = 'a';
        //return require('/data/htmlwww/docker/container/passport/config/autoload/routes.php');
        return $this->__request(__FUNCTION__, ['token' => $token]);
    }

    /**
     * @Cacheable(prefix="common-route")
     */
    public function getPermissionDatas($key = 'routes')
    {
        $passportBase = make(PassportBaseService::class);
        $routes = $passportBase->getRouteDatas();
    }
}
