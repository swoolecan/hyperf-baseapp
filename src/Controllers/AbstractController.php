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
use Swoolecan\Baseapp\Helpers\ResourceContainer;

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

    public function getRepositoryObj($code = '', $params = [])
    {
        $code = !empty($code) ? $code : get_called_class();
        return $this->resource->getObject('repository', $code, $params);
    }

    public function getServiceObj($code = '', $params = [])
    {
        $code = !empty($code) ? $code : get_called_class();
        return $this->resource->getObject('service', $code, $params);
    }

    public function getRequestObj($scene = '', $repository = null, $code = '')
    {
        //$type = empty($action) ? 'request' : 'request-' . $action;
        $code = !empty($code) ? $code : get_called_class();
        //$request = $this->resource->getObject($type, $code, false);
        $request = $this->resource->getObject('request', $code, false);
        if (empty($request)) {
            return $this->request;
        }
        if ($repository) {
            $request->setRepository($repository);
        }
        $request->setScene($scene);

        if (method_exists($request, 'validateResolved')) {
            $request->validateResolved();
        }
        return $request;
    }

    protected function success($datas, $message = 'success')
    {
        return ['code' => 200, 'message' => $message, 'datas' => $datas];
    }

    public function throwException($code, $message = null)
    {
        return $this->resource->throwException($code, $message);
    }

    public function dealCriteria($scene, $repository, $params)
    {
        return $repository->getDealSearchFields($scene, $params);
    }
}
