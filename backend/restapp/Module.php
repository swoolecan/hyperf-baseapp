<?php
namespace backend\restapp;

use Yii;
use common\components\ModuleBase;

class Module extends ModuleBase
{
    public function init()
    {   
        parent::init();
		Yii::configure($this, ['controllerMap'=> $this->getPointControllerMap('controller-restapp')]);
    }   
}
