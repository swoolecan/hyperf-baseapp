<?php

namespace Swoolecan\Baseapp\Controller\Traits;

use Yii;

trait TraitImexport
{
    public function actionImport()
    {
    	return $this->_importInfo();
    }

    public function actionExport()
    {
        return $this->_exportInfo();
    }

    protected function _importInfo()
    {
        $model = $this->getModel(true, $this->_addData());
        $data = [];
		$result = null;
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->import();
			return $this->returnResult(['status' => 200, 'message' => '操作完成']);
        }
		$data = [
			'model' => $model,
			'result' => $result,
		];

        return $this->render($this->viewPrefix . 'import', $data);
    }

	protected function _importData()
	{
		return [];
	}
}
