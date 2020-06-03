<?php

namespace common\controllers\operation;

Trait TraitView
{
	public function actionShow($id = null)
	{
		$this->frontPriv(false, true);
		return $this->actionView($id);
	}

	public function actionDetail($id = null)
	{
		$this->frontPriv(true, true);
		return $this->actionView($id, true);
	}

    public function actionView($id = null, $myInfo = false)
    {
        $fModel = $this->findModel($id);
		if ($fModel['status'] != 200) {
			return $this->returnResult($fModel);
		}
		$model = $fModel['model'];
		if ($myInfo && $model['user_id'] != $this->userInfo['id']) {
			return $this->returnResult(['status' => 400, 'message' => '您不能查看指定信息']);
		}
		$data = ['view' => $this->viewView, 'model' => $model];
        return $this->returnInfoResult($model, $data);
    }

    protected function getViewView()
    {
        return '@views/backend/common/view';
    }

	public function _getViewFields()
	{
		return [];
	}
}
