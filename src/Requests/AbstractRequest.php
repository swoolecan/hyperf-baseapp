<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Requests;

use Hyperf\Validation\Rule;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\HttpServer\Router\Dispatched;
use Swoolecan\Foundation\Requests\TraitRequest;

class AbstractRequest extends FormRequest
{
    use TraitRequest;

    public function getCurrentRoute()
    {
        return $this->getAttribute(Dispatched::class);
    }

    protected function _getKeyValues($field)
    {
        return Rule::in(array_keys($this->getRepository()->getKeyValues($field)));
    }

    protected function getRule()
    {
        return new Rule();
    }
}
