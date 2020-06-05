<?php

namespace baseapp\controllers;

trait CategoryTrait
{
	public function getViewPrefix()
	{
        return '@baseapp/views/category/';
	}

	public function actionList()
	{
		$rType = $this->getInputParams('result_type');
		if ($rType == 'tree') {
    		$datas = $this->model->groupInfos;
    		return [
    			'status' => 200, 
    			'message' => 'OK',
    			'datas' => $datas,
    		];
		}
		return parent::actionList();
	}

    public function behaviors()
    {
		$behaviors = parent::behaviors();
		unset($behaviors['verbs']);
		return $behaviors;
	}
}
