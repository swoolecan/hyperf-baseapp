<?php

namespace common\models\traits;

use Yii;

trait ActiveTrait
{
    use BaseModel;
    use Attachment;
	use ActiveData;
	use RelateData;
	use RelateDataExt;

    public function updateNum($field, $type)
    {
        $num = $type == 'add' ? 1 : -1;
        $this->updateCounters(['num_' . $field => $num]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->writeManagerLog();
        return true;
    }

	public function _deleteSoft()
	{
		$this->is_delete = 1;
		$this->update(false, ['is_delete']);
		return ['status' => 200, 'message' => 'OK', 'info' => $this];
	}

    public function _deleteReal()
    {
		$info = $this;
        $this->delete();
        return ['status' => 200, 'message' => 'OK', 'info' => $info];
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $this->writeManagerLog();
        return true;
    }

    protected function writeManagerLog()
    {
        if (Yii::$app->id != 'app-backend' || in_array(Yii::$app->controller->id, ['entrance', 'site', 'backend-upload'])) {
            return true;
        }

        $attributes = $this->attributes;
        $infos = get_object_vars($this);

        $infos = array_merge($attributes, $infos);
        $data = [];
        foreach ($infos as $key => $value) {
            if (is_array($value)) {
                $value = serialize($value);
            }

            $data[$key] = $value;
        }
        $managerInfo = Yii::$app->controller->userInfo;
        $menuInfo = Yii::$app->controller->menuInfos['currentMenu'];

        $infos = [
            'manager_id' => $managerInfo['id'],
            'manager_name' => $managerInfo['name'],
            'role' => $managerInfo['roleStr'],
            'menu_code' => $menuInfo['code'],
            'menu_name' => $menuInfo['name'],
            'data' => serialize($data),
            'ip' => Yii::$app->request->userIP,
            'created_at' => time(),
        ];

        $managerlogModel = new \backend\models\Managerlog($infos);
        Yii::$app->params['managerlogModel'] = $managerlogModel;
        $managerlogModel->insert();

        return true;
    }

	public function addInfoCheck($data, $fields)
	{
		$where = [];
		foreach ($fields as $field) {
			$where[$field] = $data[$field];
		}
		$exist = $this->getInfo(['where' => $where]);
		if ($exist) {
			return $exist;
		}
		return $this->addInfo($data);
	}

	public function addInfo($data, $keyField = 'id')
	{
		$self = new static($data);
		$self->insert(false);
		return $this->getInfo($self->$keyField, $keyField);
	}

	public static function _getFieldName($withType = false)
	{
		$datas = [];
        foreach (static::getTableSchema()->columns as $column) {
			if ($withType) {
				$datas[$column->name] = [
					'type' => $column->type,
					'comment' => $column->comment,
				];
			} else {
			    $datas[$column->name] = $column->comment;
			}
		}
		return $datas;
	}

	public function getShortTable()
	{
		return str_replace(['{{%', '}}'], ['', ''], $this->tablename());
	}

	public function getFullId($field, $ignoreStr = '')
	{
		return str_replace(['-', '_', $ignoreStr], ['', '', ''], $this->modelCode) . '-' . $field;
	}

    public function _updateSingle($params, $datas = [])
    {
        $info = $this->findOne($params['infoId']);
        if (empty($info)) {
            return ['status' => 400, 'message' => '信息不存在'];
        }
        $field = $params['field'];
        $value = $params['value'];
        if ($field == 'callback_next') {
            $value = !empty($value) ? strtotime($value) : $model->callback_again;
        }
        $info->$field = $value;
        $r =$info->update(false);

        return ['status' => 200, 'message' => 'OK'];
    }

	public function _extSql()
	{
		//$infoNumber = $this->getInfos(['select' => 'SUM(number) as number', 'where' => ['orderid' => $orderid], 'limit' => 1]);
		//$number = isset($infoNumber[0]['number']) ? $infoNumber[0]['number'] : $order->number;
		//$infoPrice = $this->getInfos(['select' => 'SUM(price * number) as price', 'where' => ['orderid' => $orderid], 'limit' => 1]);
		//$price = isset($infoPrice[0]['price']) ? $infoPrice[0]['price'] : $order->money;
	}
}
