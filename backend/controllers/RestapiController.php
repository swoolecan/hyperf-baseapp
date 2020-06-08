<?php

namespace backend\controllers;

class RestapiController extends Controller
{
    public function _addData()
    {
		return [
			'menu_code' => $this->getInputParams('menu_code'),
		];
    }

}
