<?php

namespace baseapp\models;

use Overtrue\Pinyin\Pinyin;

Trait TagTrait
{
	public $add_mul;

    public function rules()
    {
        return [
			[['name'], 'required'],
            ['name', 'unique', 'targetClass' => get_called_class(), 'message' => '该标签已存在'],
            [['name'], 'checkTagCode'],
			[['orderlist', 'status'], 'default', 'value' => 0],
            [['add_mul', 'description', 'sort', 'code'], 'safe'],
        ];
    }

	public function checkTagCode()
	{
		if (empty($this->code)) {
			$this->code = $this->_createCode($this->name);
		}
		return true;
	}

	public function addMul()
	{
		$datas = array_filter(explode("\n", $this->add_mul));
		$num = 0;
		foreach ($datas as $data) {
			$data = str_replace([' ', "\t"], ' ', $data);
			$info = explode(' ', $data);
			$name = isset($info[0]) ? trim($info[0]) : '';
			$orderlist = isset($info[1]) ? intval($info[1]) : '';
			if (empty($name)) {
				continue;
			}
			$model = self::findOne(['name' => $name]);
			if ($model) {
				$model->orderlist = $orderlist;
			} else {
				$num++;
			    $model = new self(['name' => $name]);
			}
			$model->save();
		}
		return ['status' => 200, 'message' => '成功添加了' . $num . '条标签'];
	}	

    protected function _getTemplatePointFields()
    {
        return [
			'status' => ['type' => 'changedown'],
			'listNo' => ['description'],
        ];
    }

	public function getSortInfos()
	{
		return [
		];
	}

	public function _sceneFields()
	{
		return [
			'base' => ['id', 'code', 'name'],
		];
	}

	public function createRecord($tag)
	{
		$exist = $this->getInfo($tag, 'name');
		if ($exist) {
			return $exist;
		}
		$code = $this->_createCode($tag);
		$data = ['code' => $code, 'name' => $tag];
		return $this->addInfo($data);
	}

	protected function _createCode($tag)
	{
        $code = Pinyin::trans($tag, ['delimiter' => '', 'accent' => false]);
		$info = $this->getInfo($code, 'code');
		$code = empty($info) ? $code : $code . $info['id'];
		return $code;
	}
}
