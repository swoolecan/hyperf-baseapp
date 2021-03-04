<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\RpcClient;

use Hyperf\Di\Annotation\Inject;
use Phper666\JWTAuth\JWT;

/**
 * 服务消费者
 */
class PassportRpcClient extends AbstractRpcClient
{
    /**
     * @Inject
     * @var JWT
     */
    protected $jwt;

    /**
     * 定义对应服务提供者的服务名称
     * @var string
     */
    protected $serviceName = 'PassportRpcServer';

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

    public function getUserById($id): array
    {
        return $this->__request(__FUNCTION__, ['id' => $id]);
    }

    public function getSingleAttachmentData($params): array
    {
        return $this->__request(__FUNCTION__, $params);
    }

    public function getAttachmentInfos($params): array
    {
        return $this->__request(__FUNCTION__, ['params' => $params]);
    }
}
