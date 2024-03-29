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

namespace Framework\Baseapp\Controllers;

use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Contract\ConfigInterface;
use Framework\Baseapp\Helpers\Helper;
use Framework\Baseapp\Helpers\ResourceContainer;
use Swoolecan\Foundation\Controllers\TraitController;

abstract class AbstractController
{
    use TraitController;

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
     * @var ResourceContainer
     */
    protected $resource;

    protected function success($datas, $message = 'success')
    {
        return ['code' => 200, 'message' => $message, 'datas' => $datas];
    }

    public function throwException($code, $message = null)
    {
        return $this->resource->throwException($code, $message);
    }
}
