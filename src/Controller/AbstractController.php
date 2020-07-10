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

namespace Swoolecan\Baseapp\Controller;


use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Contract\ConfigInterface;
use Swoolecan\Baseapp\Helper\Helper;
use Swoolecan\Baseapp\Helper\ResourceContainer;
use Swoolecan\Baseapp\Controller\Trait\OperationTrait;

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
     * @var ResourceContainer
     */
    protected $resource;

    public function getServiceRepo($code = '', $params = [])
    {
        $code = !empty($code) ? $code : get_called_class();
        return $this->resource->getObject('service-repo', $code);
    }

    public function getServiceObj($code = '', $params = [])
    {
        $code = !empty($code) ? $code : get_called_class();
        return $this->resource->getObject('service', $code, $params);
    }

    public function getRequestObj($action = '', $code = '')
    {
        $type = empty($action) ? 'request' : 'request-' . $action;
        $code = !empty($code) ? $code : get_called_class();
        $request = $this->resource->getObject($type, $code, false);
        if (empty($request)) {
            return $this->request;
        }

        if (method_exists($request, 'validateResolved')) {
            var_dump($request->all());
            $request->validateResolved();
        }
        return $request;
    }

    protected function success($datas, $message = 'success')
    {
        return ['code' => 200, 'message' => $message, 'datas' => $datas];
    }
}
