<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Requests;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\HttpServer\Router\Dispatched;

class AbstractRequest extends FormRequest
{
    protected $_scene;

    public function authorize(): bool
    {
        return true;
    }

    public function routeParam(string $key, $default)
    {
        $route = $this->getAttribute(Dispatched::class);
        if (is_null($route)) {
            return $default;
        }
        return array_key_exists($key, $route->params) ? $route->params[$key] : $default;
    }

    public function setScene($scene)
    {
        $this->_scene = $scene;
    }

    public function getScene()
    {
        return $this->_scene;
    }
}
