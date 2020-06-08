<?php

namespace backend\controllers;

class AttachmentController extends Controller
{
    public function confirmUpdate($model)
    {
        $model->noFile = true;
        return ;
    }

    public function getModel($forceNew = false, $data = [])
    {
        //return $this->getPointModel($this->attachmentCode, true, $params);
		$modelMid = $this->getPointModel('attachment');
        $code = $this->getInputParams('attachment_code');
		$model = $modelMid->initMark($code);
		return $model;
    }
}
