<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Helpers;

use Hyperf\Utils\Str;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Contract\ConfigInterface;
use Swoolecan\Baseapp\Helpers\SysOperation;
use Swoolecan\Baseapp\Exceptions\BusinessException;

/**
 * 系统资源
 */
Class ResourceContainer
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    protected $resources;
    protected $objects = [];
    public $params = [];

    public function __construct()
    {
        //$this->resources = SysOperation::initResourceDatas();
        $resources = $this->getResourceDatas('resource');
        $resources = isset($resources[$this->config->get('app_code')]) ? $resources[$this->config->get('app_code')] : $resources;
        $this->resources = $resources;
        //var_dump($this->bakresources);exit();
    }

    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
    }

    protected function getResourceCode($class)
    {
        $elems = explode('\\', $class);
        $count = count($elems);
        $code = $count == 4 ? $elems[3] : $elems[2];
        $type = $elems[1];

        if (is_null($type)) {
            echo 'tttttttttt-' . $class . "ooooo \n";
        }
        $type = Str::singular($type);

        //$code = str_replace(['Controller', 'Repository'], ['', ''], $code);
        $pos = strripos($code, $type);
        if ($pos !== false) {
            $code = substr($code, 0, $pos);
        }
        $code = Str::snake($code, '-');
        $code .= $count == 4 ? '-' . strtolower($elems[2]) : '';
        return $code;
    }

    public function getObject($type, $code, $params = [])//$throw = true)
    {
        $class = $this->_formatClass($type, $code);
        if (empty($class)) {
            //if ($throw) {
                throw new BusinessException(500, '资源不存在-' . $code);
            //}
            //return null;
        }

        if (isset($this->objects[$class])) {
            return $this->objects[$class];
        }
        echo $class . "\n cccccc \n";
        $obj = make($class, ['resource' => $this]);//new $class();//$type == 'model' ? new $class([], $this) : new $class($this);
        if (method_exists($obj, 'init')) {
            $obj->init($params);
        }
        echo get_class($obj) . "\n rrrrrr \n";
        $this->objects[$class] = $obj;
        return $obj;
    }

    public function _formatClass($type, $code)
    {
        if (!isset($this->resources[$code])) {
            $code = $this->getResourceCode($code);
        }
        if (!isset($this->resources[$code])) {
            return false;
        }

        $info = $this->resources[$code];
        $class = isset($info[$type]) ? $info[$type] : false;
        if (empty($class) && $type == 'service-repo') {
            $class = isset($info['service']) ? $info['service'] : (isset($info['repository']) ? $info['repository'] : '');
        }
        echo $class . "\n";
        return strval($class);
    }

    public function getIp()
    {
        $ip = $this->request->getHeader('x-real-ip');
        if (empty($ip)) {
            return '';
        }
        if (is_string($ip)) {
            return $ip;
        }
        return $ip[0];
    }

    public function throwException($code, $message = null)
    {
        throw new BusinessException($code, $message);
    }

    /**
     * @Cacheable(prefix="user")
     */
    protected function getResourceDatas($key)
    {
        $datas = $this->config->get('resource');
        return $datas;
    }
}
