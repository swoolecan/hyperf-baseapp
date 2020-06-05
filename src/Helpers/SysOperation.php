<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Helpers;

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
        $base = 'App\Controller\\';
        $code = !empty($rData['module']) ? ucfirst($rData['module']) . '\\' : '\\';
        $code .= ucfirst($rCode);
        foreach (['request', 'model', 'service', 'repository'] as $elem) {
            $elemCode = ucfirst($elem);
            $info = [];
            if ($elem == 'model') {
                $data[$elem] = "app\\{$elemCode}\\{$code}";
            /*} else if ($elem == 'repository') {
                $data[$elem] = "app\\{$elemCode}\\{$code}";*/
            } else {
                $data[$elem] = "app\\{$elemCode}\\{$code}{$elemCode}";
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
            $dResources = [
                'permission' => ['module' => 'passport'], 
                'role' => ['module' => 'passport'], 
                'resource' => ['module' => 'passport'], 
                'manager' => ['module' => 'passport'], 
            ];
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
        $baseCallback .= !empty($rData['module']) ? ucfirst($rData['module']) . '\\' : '\\';
        $baseCallback .= ucfirst($rCode) . 'Controller@';
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

    /**
     * Generate random decimals
     */
    public static function randFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * 调用文件夹所有的php文件
     */
    public static function requireDirScript($dir, $filename='')
    {
        if (is_dir($dir)) {
            $handler = opendir($dir);
            //遍历脚本文件夹下的所有文件
            while (false !== ($file = readdir($handler))) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath) && substr($file,-4) == '.php') {
                        if ($filename !== '' && basename($fullpath, '.php') === $filename) {
                            require_once($fullpath);
                        } else {
                            require_once($fullpath);
                        }
                    } else {
                        require_dir_script($fullpath);
                    }
                }
            }
            //关闭文件夹
            closedir($handler);
        }
    }

    /**
     * copy
     */
    public static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
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
            'permission' => ['module' => 'passport'], 
            'role' => ['module' => 'passport'], 
            'resource' => ['module' => 'passport'], 
            'manager' => ['module' => 'passport'], 
        ];
    }
}
