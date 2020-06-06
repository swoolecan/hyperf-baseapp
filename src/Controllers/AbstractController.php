<?php

declare(strict_types = 1);

/**
 * This file is an abstract controller for hyperf
 *
 * @link     http://http://home.canliang.wang/
 * @document http://wiki.canliang.wang/
 * @contact  iamwangcan@gmail.com
 * @license  https://github.com/swoolecan/hyperf-baseapp/blob/master/LICENSE.md
 */

namespace Swoolecan\Baseapp\Controllers;


use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Contract\ConfigInterface;
use Swoolecan\Baseapp\Helpers\Helper;
use Swoolecan\Baseapp\Helpers\Resource;
use Swoolecan\Baseapp\Exceptions\BusinessException;
use Swoolecan\Baseapp\Controllers\Traits\OperationTrait;

abstract class AbstractController
{
    use OperationTrait;

    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @Inject
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @Inject                
     * @var Helper            
     */
    protected $helper;

    /**
     * @Inject                
     * @var Resource
     */
    protected $resource;

    public function getServiceObj()
    {
        $this->serviceObj = $this->resource->getObject('service', get_called_class());
    }

    protected function throwException($code, $message = null)
    {
        throw new BusinessException($code, $message);
    }
}
