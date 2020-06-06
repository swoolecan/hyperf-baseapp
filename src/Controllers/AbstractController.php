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
use Hyperf\Utils\Str;
use Swoolecan\Baseapp\Helpers\SysOperation;
use Swoolecan\Baseapp\Helpers\Helper;
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
        return $this->_getRelateObj('model', $code);
    }

    protected function getRelateRequest($code = null)
    {
        return $this->_getRelateObj('request', $code);
    }

    protected function getRelateRepository($code = null)
    {
        return $this->_getRelateObj('repository', $code);
    }

    protected function _getRelateObj($type, $code)
    {
        $info = is_null($code) ? $this->resourceInfo : $this->resources[$code];
        $class = $info[$type];
        return new $class();
    }

    protected function throwException($code, $message = null)
    {
        throw new BusinessException($code, $message);
    }
}
