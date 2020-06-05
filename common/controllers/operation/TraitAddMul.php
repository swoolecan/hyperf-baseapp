<?php

namespace common\controllers\operation;

use Yii;

trait TraitAddMul
{
	public function actionAddMul()
	{
		return $this->_addMulInfo();
    }

    protected function _addMulInfo()
    {
		$addData = $this->_addData();
		if (isset($addData['status']) && $addData['status'] == 400) {
			return $this->returnResult($addData);
		}
        $model = $this->getModel(true, $addData);
		if ($this->currentMethod == 'post') {
    		$loadData = $this->loadDatas($model);
			if ($loadData) {
			   	$result = $model->addMul();
    			return $this->returnResult($result);
            }
		}
		//var_dump($this->addView);exit();
		$viewData = ['view' => $this->addMulView, 'currentView' => $this->viewPrefix, 'type' => 'add-mul'];
		return $this->returnInfoResult($model, $viewData);
    }

    protected function getAddMulView()
    {
        return '@views/backend/common/change';
    }
}
