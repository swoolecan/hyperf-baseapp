<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Helpers;

use Hyperf\Utils\Str;
use EasyWeChat\Factory;

/**
 * 系统操作相关功能
 */
Class SysOperation
{
    /**
     * init resources
     */
    public static function initResourceDatas()
    {
        $resourceFile = self::getCachePath('resource');
        if (!file_exists($resourceFile)) {
            $dResources = self::getDefaultResources();
            self::cacheResources($dResources);
        }
        return require($resourceFile);
    }

    public static function cacheResources($datas)
    {
        $results = [];
        foreach ($datas as $rCode => $rData) {
            $code = empty($rData['module']) ? $rCode : "{$rCode}-{$rData['module']}";
            $results[$code] = self::_cacheResource($rCode, $rData);
        }
        $resourceFile = self::getCachePath('resource');
        $str = "<?php\nreturn " . var_export($results, true) . ' ;';
        file_put_contents($resourceFile, $str);
        return true;
    }

    public static function _cacheResource($rCode, $rData)
    {
        $data = [];
        $code = !empty($rData['module']) ? self::toUpper($rData['module']) . '\\' : '';
        $code .= self::toUpper($rCode);

        foreach (['request', 'resource', 'model', 'service', 'repository'] as $elem) {
            if (isset($rData[$elem]) && !empty($rData[$elem]) && !is_array($rData[$elem])) {
                $data[$elem] = $rData[$elem];
                continue;
            }
            $elemCode = self::toUpper($elem);
            if ($elem == 'model') {
                $class = "App\\{$elemCode}\\{$code}";
            } else {
                $class = "App\\{$elemCode}\\{$code}{$elemCode}";
            }
            if (class_exists($class)) {
                $data[$elem] = $class;
            }

            if (in_array($elem, ['request', 'resource']) && isset($rData[$elem]) && is_array($rData[$elem])) {
                foreach ($rData[$elem] as $key => $value) {
                    $codeExt = $code . ucfirst($key);
                    $class = !empty($value) ? $value : "App\\{$elemCode}\\{$codeExt}{$elemCode}";
                    if (class_exists($class)) {
                        $data[$elem . '-' . $key] = $class;
                    }
                }
            }

        }
        return $data;
    }

    /**
     * init routes
     */
    public static function initRouteDatas()
    {
        $routeFile = self::getCachePath('route');
        if (!file_exists($routeFile)) {
            $dResources = self::getDefaultResources();
            self::cacheRoutes($dResources);
        }
        return require($routeFile);
    }

    public static function cacheRoutes($datas)
    {
        $results = [];
        foreach ($datas as $rCode => $rData) {
            $results[$rCode] = self::_cacheRoute($rCode, $rData);
        }
        $routeFile = self::getCachePath('route');
        $str = "<?php\nreturn " . var_export($results, true) . ' ;';
        file_put_contents($routeFile, $str);
        return true;
    }

    public static function _cacheRoute($rCode, $rData)
    {
        $actionMethods = [
            'index' => 'get',
            'put' => 'put',
            'store' => 'post',
            'show' => 'get',
            'delete' => 'post'
        ];
        $data = [];
        $basePath = !empty($rData['module']) ? "/{$rData['module']}" : '';
        $baseCallback = 'App\Controller\\';
        $baseCallback .= !empty($rData['module']) ? self::toUpper($rData['module']) . '\\' : '\\';
        $baseCallback .= self::toUpper($rCode) . 'Controller@';
        foreach (['index', 'put', 'store', 'show', 'delete'] as $action) {
            $method = !empty($rData['method']) ? $rData['method'] : $actionMethods[$action];
            $method = (array) explode(',', $method);
            foreach ($method as & $value) {
                $value = strtoupper($value);
            }
            $path = $basePath . "/{$rCode}s";
            $path .= in_array($action, ['put', 'delete', 'show']) ? "/{id:\d+}" : '';
            $data[$action] = [
                'method' => $method,
                'path' => $path,
                'callback' => $baseCallback . $action,
            ];
        }
        return $data;
    }

    public static function getApp($config)
    {
        $config = [
            'app_id' => $config['app_id'],
            'secret' => $config['secret'],
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => '/tmp/wechat.log',
            ],
        ];
        return Factory::officialAccount($config);
    }

    public static function sendNotice($exception)
    {
        $config = \common\helpers\InitFormat::getBaseParams('config/params-local.php');
        $config = $config['errorNotice'];
        $class = get_class($exception);
        if ($config['noNotice'] || in_array($class, $config['ignore']) || $exception->statusCode == '200') {
            return ;
        }
        $app = self::getApp($config);

        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $url = substr($url, 0, 50);
        $params = [
            'template_id' => $config['template_id'],
            'data' => [
                'first' => '测试错误通知-' . $exception->getMessage(),
                'time' => date('Y-m-d H:i:s'),
                'ip_list' => Yii::$app->id,
                'sec_type' => get_class($exception),
                'remark' => $exception->getLine() . "--{$url}"  . "\n" . $exception->getFile(),
            ],
        ];
        foreach ($config['touser'] as $touser) {
            $params['touser'] = $touser;
            $app->template_message->send($params);
        }
    }

    protected static function getCachePath($type)
    {
        return BASE_PATH . '/runtime/cache/' . $type . '.php';
    }

    protected static function getDefaultResources()
    {
        return [
            'entrance' => [
                'request' => [
                    'signupin' => 'App\\Request\\EntranceSignupinRequest',
                    'token' => 'App\\Request\\EntranceTokenRequest',
                ],
            ],
            'easysms' => ['service' => 'Swoolecan\\Baseapp\\Services\\EasysmsService'],
            'user' => ['module' => 'passport'], 
            'permission' => ['module' => 'passport'], 
            'role' => ['module' => 'passport'], 
            'resource' => ['module' => 'passport'], 
            'manager-backend' => ['module' => 'passport'], 
        ];
    }

    public static function toUpper($str)
    {
        return Str::studly($str);
    }

    public static function setCacheElems($type, $datas)
    {
        $cacheFile = self::getCachePath('elem-' . $type);
        $str = "<?php\nreturn " . var_export($datas, true) . ' ;';
        file_put_contents($cacheFile, $str);
        return true;
    }

    public static function getCacheElems($type)
    {
        $cacheFile = self::getCachePath('elem-' . $type);
        if (!file_exists($cacheFile)) {
            return [];
        }
        return require($cacheFile);
    }
}
