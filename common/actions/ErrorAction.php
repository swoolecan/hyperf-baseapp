<?php

namespace common\actions;

use Yii;
use yii\web\Response;
use yii\web\ErrorAction as ErrorActionBase;

class ErrorAction extends ErrorActionBase
{
    protected function beforeRun()
    {
        Yii::$app->layout = '@views/base/main';
            $this->controller->layout = null;
        if (!empty($this->controller)) {
            $this->controller->layout = null;
        }
        return true;
    }

    protected function renderAjaxResponse()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; 
        return ['status' => 400, 'message' => $this->getExceptionName() . ': ' . $this->getExceptionMessage()];
    }
}
