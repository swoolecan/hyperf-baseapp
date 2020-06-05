<?php

namespace common\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;

class SpreadRecordAction extends Action
{
	public $view;

    protected function run()
    {
        //Yii::$app->response->format = Response::FORMAT_JSON; 
        $channel = Yii::$app->getRequest()->get('schannel');
        $method = Yii::$app->getRequest()->method;
        if (empty($channel) || $method != 'GET') {
			return '';//[];
		}

		$haveSpread = Yii::getAlias('@spread', false);
		if (empty($haveSpread)) {
			return '';//[];
		}

        $return = $this->controller->getPointModel('spread-record')->writeVisitLog();
		return '';//[];
    }
}
