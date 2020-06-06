<?php

namespace Swoolecan\Baseapp\Controllers\Traits;

use Yii;

Trait TraitListinfoTree
{
    public function actionListinfoTree()
    {
        $this->searchModel = $this->model;
        return $this->_listinfoTree($this->searchModel);
    }

    /**
     * Lists tree infos,
     * @return mixed
     */
    public function _listinfoTree($model)
    {
        $infos = $model->getFormatedInfos();

        $this->setReturnUrl(Yii::$app->request->url);
        return $this->render('@views/backend/common/listinfo_tree', [
            'model' => $model,
            'currentView' => $this->viewPrefix,
            'infos' => $infos,
        ]);
    }
}
