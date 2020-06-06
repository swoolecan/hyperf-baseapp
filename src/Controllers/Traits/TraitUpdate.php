<?php

namespace Swoolecan\Baseapp\Controllers\Traits;

use Yii;

trait TraitUpdate
{
    public function update(RequestInterface $request, $id)
    {
        $data = $request->all();
        //$permissions = $request->input('permissions', []);
        $result = $this->getRelateModel()->find($id);
        if (!$result) {
            throw new BusinessException(404);
        }
        //unset($data['permissions']);
        //$result->update($data);
        //$result->syncPermissions($permissions);
        return $result;
    }

	public function actionEdit()
	{
		$this->frontPriv(false);
		$id = $this->getInputParams('id', 'postget');
		$info = $this->model->getInfo(['where' => ['id' => $id, 'user_id' => $this->userInfo['id']]]);
		if (empty($info)) {
			return $this->returnResult(['status' => 400, 'message' => '信息有误']);
		}
		return $this->_updateInfo($info, true);
	}

    public function actionUpdate($id = 0)
    {
		$updateBatch = $this->getInputParams('update_batch', 'postget');
        if (!empty($updateBatch)) {
            return $this->_updateBatch();
        }
        $fModel = $this->findModel($id);
		if ($fModel['status'] != 200) {
			return $this->returnResult($fModel);
		}
		$model = $fModel['model'];
		$updateRelate = $this->getInputParams('update_relate', 'postget');
        if (!empty($updateRelate) && $this->currentMethod != 'post') {
            return $this->render($this->viewPrefix . 'update-relate', ['model' => $model]);
        }

        return $this->_updateInfo($model);
    }

    protected function _updateInfo($model, $checkUserId = false)
    {
        $scenario = $this->_getScenario();
        if (!empty($scenario)) {
            $model->setScenario($scenario);
        }

		if ($this->currentMethod == 'post') {
    		$loadData = $this->loadDatas($model);
			$priv = $model->dealPriv('update', 'submit');
			$priv = $priv && $checkUserId && isset($loadData['user_id'])? $loadData['user_id'] == $this->userInfo['id'] : $priv;
            if ($priv && $loadData && $model->save()) {
    			return $this->returnResult(['status' => 200, 'message' => '操作完成', 'datas' => $model->restSimpleData($model)]);
			} else {
                $eData = $model->_formatFailResult('编辑信息失败');
    			return $this->returnResult($eData);
			}
		}
		$viewData = ['view' => $this->updateView, 'currentView' => $this->viewPrefix, 'type' => 'update'];
		return $this->returnInfoResult($model, $viewData);
    }

	public function _updateBatch()
	{
        if ($this->currentMethod != 'post') {
            return ['status' => 400, 'message' => '请求方法错误'];
        }

		//$ids = empty($id) ? array_filter(explode(',', $this->getInputPosts('selections'))) : (array) $id;
		$ids = $this->getInputParams('selections', 'postget');
		$ids = is_array($ids) ? $ids : array_filter(explode(',', $ids));
		$count = count($ids);
		if (empty($count)) {
			return $this->returnResult(['status' => 400, 'message' => '没有要批量编辑的信息']);
		}
		$fields = $this->getInputParams('field_batch', 'postget');
		if (empty($fields)) {
			return $this->returnResult(['status' => 400, 'message' => '请指定批量编辑得字段']);
		}

		$sourceValues = $this->getInputParams('value_batch', 'postget');
        $values = count($ids) == 1 ? [$sourceValues] : $sourceValues;
		$updateNum = 0;
		foreach ($ids as $id) {
		    $fModel = $this->findModel($id);
			if ($fModel['status'] != 200) {
				continue;
			}
			$model = $fModel['model'];
		    $confirmUpdate = $this->confirmUpdate($model);
		    if ($confirmUpdate) {
				continue;
			    //return ['status' => 400, 'message' => '信息已锁定不能修改'];
		    }
			$fields = (array) $fields;
			foreach ($fields as $key => $field) {
				$model->$field = $values[$key];
			}
			$r = $model->update(false, $fields);
			$updateNum++;
		}
		return $this->returnResult(['status' => 200, 'message' => "指定编辑{$count}条信息，实际编辑{$updateNum}条信息"]);
	}

    protected function getUpdateView()
    {
        return '@views/backend/common/change';
    }

	protected function confirmUpdate($info)
	{
		return false;
	}

    protected function _updateByAjax()
    {
    }

    /*protected function _updateByAjax()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($this->currentMethod != 'post') {
            return ['status' => 400, 'message' => '请求方法错误'];
        }

        $id = Yii::$app->request->post('info_id');
        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        if (empty($id) || empty($field)) {
            return ['status' => 400, 'message' => '参数错误'];
        }

        $fModel = $this->findModel($id);
        if ($fModel['status'] != 200) {
            return $fModel;
        }
		$model = $fModel['model'];
		$confirmUpdate = $this->confirmUpdate($model);
		if ($confirmUpdate) {
			return ['status' => 400, 'message' => '信息已锁定不能修改'];
		}
        $model->$field = $value;
        $model->update(false);

        return ['status' => 200, 'message' => 'OK'];
	}*/
}
