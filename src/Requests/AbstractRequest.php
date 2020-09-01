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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $scene = $this->getScene();
        $method = "_{$scene}Rule";
        echo $method; 
        if (method_exists($this, $method)) {
            echo 'ffffffff';
            return $this->$method();
        }
        echo 'oooooo';
        return [];
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
