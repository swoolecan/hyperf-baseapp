<?php

namespace backend\controllers;

class FsceneMenuController extends Controller
{
    public function _addData()
    {
		$fsceneCode = $this->getInputParams('fscene_code');
		$fscene = $this->getPointModel('fscene')->getInfo($fsceneCode, 'code');
		if (empty($fscene)) {
			return ['status' => 400, 'message' => '信息有误'];
		}
        $data = [ 
            'fscene_code' => $fsceneCode, 
        ];  
        return $data;
    }

}
