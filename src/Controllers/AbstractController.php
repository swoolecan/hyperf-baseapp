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


use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Contract\ConfigInterface;
use Psr\SimpleCache\CacheInterface;
use Hyperf\Utils\Str;
use Swoolecan\Baseapp\Helpers\SysOperation;

abstract class AbstractController
{
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

    protected $resourceCode;
    protected $resourceInfo;
    protected $resources;

    public function __construct()
    {
        $this->resources = SysOperation::initResourceDatas();
        $rCode = $this->getResourceCode();
        $this->resourceCode = $rCode;
        $this->resourceInfo = isset($this->resources[$rCode]) ? $this->resources[$rCode] : [];
    }

    protected function getResourceCode()
    {
        $class = get_called_class();
        $elems = explode('\\', $class);
        $count = count($elems);
        $code = $count == 4 ? $elems[3] : $elems[2];

        $code = str_replace('Controller', '', $code);
        $code = Str::snake($code, '-');
        $code .= $count == 4 ? '-' . strtolower($elems[2]) : '';
        return $code;
    }

    protected function getRelateModel($code = null)
    {
        $info = is_null($code) ? $this->resourceInfo : $this->resources[$code];
        $class = $info['model'];
        return new $class();
    }
}
