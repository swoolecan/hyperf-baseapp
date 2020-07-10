<?php

namespace Swoolecan\Baseapp\Controller\Traits;

Trait TraitView
{
    public function show($id)
    {
        $result = $this->getServiceRepo()->find($id);
        if (!$result) {
            return $this->resource->throwException(404);
        }
        //$result->permissions;
        return $result;
    }

	/*public function actionShow($id = null)
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

	public function _getViewFields()
	{
		return [];
    }*/
}
