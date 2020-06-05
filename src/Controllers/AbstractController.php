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

    /**
     * @Inject                
     * @var Helper            
     */
    protected $helper;

    protected $resourceCode;
    protected $resourceInfo;
    protected $resources;

    public function index()
    {
        $params = $this->request->all();
        
        $pageSize = $this->request->input('per_page', 15);
        $params = [];
        print_r($this->getRelateModel());
        $list = $this->getRelateModel()->getList($params, (int) $pageSize);
        return $list;
    }

    public function store(RequestInterface $request)
    {
        $data = $request->all();
        //$permissions = $request->input('permissions', []);
        //unset($data['permissions']);
        $result = $this->getRelateModel()->create($data);
        //$result->permissions()->sync($permissions);
        return $result;
    }

    public function show($id)
    {
        $result = $this->getRelateModel()->find($id);
        if (!$result) {
            throw new BusinessException(404);
        }
        //$result->permissions;
        return $result;
    }

    public function update(RequestInterface $request, $id)
    {
        $data = $request->all();
        //$permissions = $request->input('permissions', []);
        $result = $this->getRelateModel()->find($id);
        if (!$result) {
            throw new BusinessException(404);
        }
        //unset($data['permissions']);
        //$result->update($data);
        //$result->syncPermissions($permissions);
        return $result;
    }

    public function destroy($id)
    {
        $result = $this->getRelateModel()->find($id);
        if (!$result) {
            throw new BusinessException(404);
        }
        return $result->delete();
    }


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
