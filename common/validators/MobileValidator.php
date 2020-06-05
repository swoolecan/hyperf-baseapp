<?php

namespace common\validators;

use yii\validators\Validator;

class MobileValidator extends Validator
{
    public $pattern = '/1[3456789]{1}\d{9}$/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = '手机号码格式有误';
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $valid = strlen($value) != 11 ? false : preg_match($this->pattern, $value);

        return $valid ? null : [$this->message, []];
    }
}
