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

    public function getCacheData($resource, $key, $keyField = 'id')
    {
        $repository = $this->resource->getObject('repository', $resource);
        return $repository->getCacheData($resource, $key);
        $app = ucfirst($app);
        $class = "\App\RpcServer\\{$app}RpcServer";
        $client = make($class);
        return $client->getCacheData($app, $resource, $key, $keyField);
    }

    public function getSingleAttachmentData($params)
    {
        $repository = $this->resource->getObject('repository', 'attachmentInfo');
        return $repository->getSingleData($params);
    }
}
