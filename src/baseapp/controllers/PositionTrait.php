<?php

namespace baseapp\controllers;

trait PositionTrait
{
	public function actionList($id = NULL)
	{
		$where = ['status' => 1];
		if (!empty($this->getInputParams('sort'))) {
			$where['sort'] = $this->getInputParams('sort');
		}
		if (!empty($this->getInputParams('owner_mark'))) {
			$where['sort'] = $this->getInputParams('owner_mark');
		}
		$number = $this->getInputParams('number');
		$number = empty($number) ? 3 : $number;

		$datas = $this->getPointDatas($this->modelCode, ['where' => $where, 'limit' => $number]);
		return [
			'status' => 200,
			'message' => 'OK',
			'datas' => $this->_formatListSimple($datas),
		];

	}

	public function getViewPrefix()
	{
        return '@baseapp/views/position/';
	}
}
