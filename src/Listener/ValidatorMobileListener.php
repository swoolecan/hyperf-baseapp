<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Listeners;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;

class ValidatorMobileListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event)
    {
        /**  @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;
        // 注册了 mobile 验证器
        $validatorFactory->extend('mobile', function ($attribute, $value, $parameters, $validator) {
            $pattern = '/1[3456789]{1}\d{9}$/';
            $return = strlen($value) != 11 ? false : preg_match($pattern, $value);
            return (boolean) $return;
            //$this->message = '手机号码格式有误';
            //return $valid ? null : [$this->message, []];
        });
        // 当创建一个自定义验证规则时，你可能有时候需要为错误信息定义自定义占位符这里扩展了 :mobile 占位符
        $validatorFactory->replacer('mobile', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':mobile', $attribute, $message);
        });
    }
}
