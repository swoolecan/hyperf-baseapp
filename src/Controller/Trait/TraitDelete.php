<?php

namespace Swoolecan\Baseapp\Controllers\Trait;

use Yii;

trait TraitDelete
{
    public function destroy($id)
    {
        $result = $this->getRelateModel()->find($id);
        if (!$result) {
            throw new BusinessException(404);
        }
        return $result->delete();
    }

    public function actionDelete($id = '')
    {
        return $this->_deleteInfo($id);
    }

    public function actionRemove($id = '')
    {
		$id = $this->getInputParams('id', 'postget');
        return $this->_deleteInfo($id, true);
    }

    protected function _deleteInfo($id, $myInfo = false)
    {
		//$ids = empty($id) ? array_filter(explode(',', $this->getInputPosts('selections'))) : (array) $id;
		$ids = empty($id) ? array_filter(explode(',', $this->getInputParams('selections'))) : (array) $id;
		$count = count($ids);
		if (empty($count)) {
			return $this->returnResult(['status' => 400, 'message' => '没有指定要删除的信息']);
		}
		$delNum = 0;
		foreach ($ids as $id) {
		    $fModel = $this->findModel($id);
			if ($fModel['status'] == 200) {
			    $model = $fModel['model'];
			    if ($myInfo && $model['user_id'] != $this->userInfo['id']) {
				    return $this->returnResult(['status' => 400, 'message' => '您不能删除指定信息']);
			    }
		        $model->delete();
				$delNum++;
			}
		}
		return $this->returnResult(['status' => 200, 'message' => "指定删除{$count}条信息，实际删除{$delNum}条信息"]);
    }
}
