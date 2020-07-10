<?php

namespace Swoolecan\Baseapp\Controller\Traits;

use Yii;

trait TraitAdd
{
    public function store(RequestInterface $request)
    {
        $data = $request->all();
        //$permissions = $request->input('permissions', []);
        //unset($data['permissions']);
        $result = $this->getRelateModel()->create($data);
        //$result->permissions()->sync($permissions);
        return $result;
    }

	public function actionCreate()
	{
		$this->frontPriv();
		return $this->actionAdd();
	}

    public function actionAdd()
    {
		$addData = $this->_addData();
		if (isset($addData['status']) && in_array($addData['status'], [401, 400, 403])) {
			return $this->returnResult($addData);
		}
        $model = $this->getModel(true, $addData);
		if ($this->currentMethod == 'post') {
		    $addRelate = $this->getInputParams('add_relate', 'postget');
    		$loadData = $this->loadDatas($model, $addRelate);
			$priv = $model->dealPriv('add', 'submit');
            if ($priv && $loadData && $model->save()) {
    			//$model->redirectUrl = empty($model->redirectUrl) ? ['view', 'id' => $model->id] : $model->redirectUrl;
				$data = [
					'status' => 200, 
					'message' => '操作完成', 
					'datas' => $model->restSimpleData($model),
					'content' => $addRelate ? $this->renderForAjax($model) : '',
				];
    			return $this->returnResult($data);
			} else {
                $eData = $model->_formatFailResult('添加信息失败');
    			return $this->returnResult($eData);
			}
		}
		//var_dump($this->addView);exit();
		$viewData = ['view' => $this->addView, 'currentView' => $this->viewPrefix, 'type' => 'add'];
		return $this->returnInfoResult($model, $viewData);
    }

    protected function _addData()
    {
        return [];
    }
}
