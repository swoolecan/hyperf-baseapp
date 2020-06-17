<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Helpers;

use Hyperf\Utils\Str;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Swoolecan\Baseapp\Helpers\SysOperation;
use Swoolecan\Baseapp\Exceptions\BusinessException;

/**
 * 系统资源
 */
Class Resource
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    protected $resources;
    protected $objects = [];
    public $params = [];

    public function __construct()
    {
        $this->resources = SysOperation::initResourceDatas();
    }

    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
    }

    protected function getResourceCode($class)
    {
        echo $class . 'iiiiiii';
        $elems = explode('\\', $class);
        $count = count($elems);
        $code = $count == 4 ? $elems[3] : $elems[2];
        $type = $elems[1];

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

    public function getObject($type, $code, $throw = true)
    {
        $class = $this->_formatClass($type, $code, $throw);
        if (empty($class)) {
            if ($throw) {
                throw new BusinessException(500, '资源不存在-' . $code);
            }
            return null;
        }

        if (isset($this->objects[$class])) {
            return $this->objects[$class];
        }
        //echo $class . "\n cccccc \n";
        $obj = make($class, ['resource' => $this]);//new $class();//$type == 'model' ? new $class([], $this) : new $class($this);
        if (method_exists($obj, 'init')) {
            $obj->init($this);
        }
        //echo get_class($obj) . "\n rrrrrr \n";
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
        $class = $type == 'service-repo' ? $info['service'] : $info[$type];
        if ($type == 'service-repo' && !class_exists($class)) {
            $class = $info['repository'];
        }
        return $class;
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
}
