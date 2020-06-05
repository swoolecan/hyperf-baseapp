<?php

namespace common\actions;

use Yii;
use yii\base\Action;
use Endroid\QrCode\QrCode;

class QrcodeAction extends Action
{
	public $view;

    protected function run()
    {
		$text = Yii::$app->request->get('text');
		$qrCode = new QrCode($text);
		header('Content-Type: image/png');
		echo $qrCode->writeString();
		exit();
	}
}
