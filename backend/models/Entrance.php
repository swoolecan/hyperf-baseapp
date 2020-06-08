<?php

namespace backend\models;

use common\models\BaseModelNotable;
use baseapp\models\EntranceTrait;

class Entrance extends BaseModelNotable
{
	use EntranceTrait;

    public function rules()
    {
        return [
            [['name', 'password'], 'required', 'on' => ['signin']],
            ['password', 'validatePassword', 'on' => ['signin']],
		];
	}

    public function getUserInfo()
    {
		static $info;
        if ($info === null) {
            $info = $this->getPointModel('manager')->getInfo($this->name, 'name');
        }
        return $info;
    }

	public function getNameField()
	{
		return 'name';
	}

	public function getUserPlats()
	{
		return [];
	}
}
