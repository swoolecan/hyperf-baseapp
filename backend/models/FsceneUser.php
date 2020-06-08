<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

class FsceneUser extends BaseModel
{
	protected function _afterSaveOpe($infert, $changedAttributes)
	{
		return true;
	}

    public function rules()
    {
        return [
        ];
    }

	public function getSortInfos()
	{
		return $this->getPointModel('fscene')->getSortInfos();
	}

    protected function _getTemplatePointFields()
    {
        $elems = [
        ];
        return $elems;
    }

	public function formatOperation($view)
	{
		return $this->visitUrl;
	}

	public function createRecord($data)
	{
		$checkField = true;
		foreach (['fscene_code', 'user_id', 'sort'] as $field) {
			if (!isset($data[$field])) {
				return false;
			}
		}
		$exist = $this->getInfo(['where' => $data]);
		if ($exist) {
			$exist->status = 1;
			$exist->lastpriv_at = time();
			$exist->update(false);
			return $exist;
		}
		$data['lastpriv_at'] = time();
		$data['status'] = 1;
		return $this->addInfo($data);
	}
}
