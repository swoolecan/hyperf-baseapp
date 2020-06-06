<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Helpers;

use Hyperf\Utils\Str;
use Swoolecan\Baseapp\Helpers\SysOperation;
use Swoolecan\Baseapp\Exceptions\BusinessException;

/**
 * 系统资源
 */
Class Resource
{
    protected $resources;
    protected $objects = [];

    public function __construct()
    {
        $this->resources = SysOperation::initResourceDatas();
    }

    protected function getResourceCode($class)
    {
        $elems = explode('\\', $class);
        $count = count($elems);
        $code = $count == 4 ? $elems[3] : $elems[2];
        $type = $elems[1];
        $type = Str::singular($type);

        $code = str_replace(['Controller', 'Repository'], ['', ''], $code);
        $pos = strripos($code, $type);
        if ($pos !== false) {
            $code = substr($code, 0, $pos);
        }
        $code = Str::snake($code, '-');
        $code .= $count == 4 ? '-' . strtolower($elems[2]) : '';
        return $code;
    }

    public function getObject($type, $code, $forceNew = false)
    {
        if (!isset($this->resources[$code])) {
            $code = $this->getResourceCode($code);
        }
        if (!isset($this->resources[$code])) {
            throw new BusinessException(500, '资源不存在-' . $code);
        }

        $info = $this->resources[$code];
        $class = $info[$type];
        if ($type == 'service' && !class_exists($class)) {
            $class = $info['repository'];
        }
        if (empty($forceNew) && isset($this->objects[$class])) {
            return $this->objects[$class];
        }
        $obj = $type == 'model' ? new $class([], $this) : new $class($this);
        $this->objects[$class] = $obj;
        return $obj;
    }
}
