<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Helpers;

use Hyperf\Utils\Str;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Contract\ConfigInterface;
use Framework\Baseapp\Exceptions\BusinessException;
use Swoolecan\Foundation\Helpers\TraitResourceContainer;

/**
 * 系统资源
 */
Class ResourceContainer
{
    use TraitResourceContainer;

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
    public $appCode;
    public $params = [];

    public function __construct()
    {
        //$this->appCode = $appCode = $this->config->get('app_code');
        $resources = $this->getResourceDatas('resources');
        if (empty($resources)) {
            $this->throwException(500, '应用资源不存在-' . $appCode);
        }
        $this->resources = $resources;
    }

    protected function getAppcode()
    {
        return $this->config->get('app_code');
    }

    /**
     * @Cacheable(prefix="common-resource")
     */
    protected function getResourceDatas($key = 'resources')
    {
        $datas = $this->config->get('resource');
        return $datas;
    }

    /**
     */
    protected function _routeDatas($key)
    {
     //* @Cacheable(prefix="common-route")
        //$routes = require('/data/htmlwww/docker/container/passport/config/autoload/routes.php');
        //if ($this->appCode == 'passport') {
        //}
        $return = $this->config->get('routes');
        //print_r($return);exit();
        return $return;
        return null;
    }

    public function throwException($code, $message = null)
    {
        throw new BusinessException($code, $message);
    }

    public function strOperation($string, $operation, $params = [])
    {
        switch ($operation) {
        case 'singular':
            return Str::singular($string);
        case 'studly':
            return Str::studly($string);
        }
    }

    public function getObjectByClass($class)
    {
        return make($class);
    }
}
