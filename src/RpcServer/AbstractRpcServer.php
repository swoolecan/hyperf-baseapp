<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\RpcServer;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
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
}
