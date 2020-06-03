<?php

namespace common\actions;

use Yii;
use yii\base\Action;

class WechatOnlyAction extends Action
{
	public $view;

    protected function run()
    {
        $this->controller->layout = false;
        return $this->controller->render($this->view);
    }
}
