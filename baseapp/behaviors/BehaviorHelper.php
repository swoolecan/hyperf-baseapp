<?php

namespace baseapp\behaviors;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class BehaviorHelper
{
	/**
	 * @param $codes array behavior代码
	 */
    public static function behaviors($class, $codes)
    {
        static $datas;
        if (isset($datas[$class])) {
            return $datas[$class];
        }

        $infos = [];
        foreach ($codes as $key => $code) {
            $code = is_array($code) ? $key : $code;
            $params = is_array($code) ? $code : [];
            $infos[$code] = self::getBehavior($code, $params);
        }

        $datas[$class] = $infos;
        return $infos;
    }

    protected static function getBehavior($code, $params)
    {
        $callables = ['timestamp'];
        if (in_array($code, $callables)) {
            $method = "_{$code}Method";
            return self::$method($params);
        }

        $class = ucfirst($code);
        $class = "\baseapp\behaviors\\{$class}Behavior";
        $behavior = empty($params) ? ['class' => $class] : ['class' => $class, $params];
        return $behavior;
    }

    protected static function _timestampMethod($params)
    {
        $createdField = isset($params['createdField']) ? $params['createdField'] : 'created_at';
        $updatedField = isset($params['updatedField']) ? $params['updatedField'] : 'updated_at';
        return [
            'class' => TimestampBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => [$createdField, $updatedField],
                ActiveRecord::EVENT_BEFORE_UPDATE => [$updatedField],
            ],
        ];
    }
}
