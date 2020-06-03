<?php

namespace common\helpers;

use Yii;

class ClassHelper
{
	public static function getMethods($className)
	{
		if (empty($className)) {
			var_dump($className);exit();
		}
		//print_r(get_class_methods($className));
		$ref = new \ReflectionClass($className);//Class1 可以为对象实例 $class = new Class();
		$methods = $ref->getMethods();
		$datas = [];
		foreach ($methods as $info) {
			$datas[$info->class][] = $info->name;
		}
		return $datas;
	}

	public static function checkClassExist($class)
	{
		$class = str_replace('\\', '/', $class);
		$file = Yii::getAlias('@' . $class . '.php', false);
		if (file_exists($file)) {
			return true;
		}
		return false;
	}
}

