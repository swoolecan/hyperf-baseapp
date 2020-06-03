<?php

namespace common\components;

use Yii;
use yii\helpers\Url;

class ModuleBase extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $defaultRoute = '';
    public $currentCityCode;
    public $cityCodeValid = true;

    public function init()
    {
        parent::init();
        $this->layout = 'main';
    }

    public function getPointControllerMap($app)
    {   
        return \common\helpers\InitFormat::runtimeParams($app);
    }
}
