<?php

namespace Swoolecan\Baseapp\Controllers\Traits;

Trait TraitListinfo
{
    public function index()
    {
        $params = $this->request->all();
        
        $pageSize = $this->request->input('per_page', 15);
        $params = [];
        $list = $this->getServiceRepo()->all();//null, $params, (int) $pageSize);
        return $list;
    }

    public function treeListinfos()
    {
        //$infos = $model->getFormatedInfos();
        $infos = $this->getService()->getTreeInfos();//null, $params, (int) $pageSize);

        return $infos;
    }

	/*public function actionList()
	{
		$this->frontPriv(false);
		return $this->actionListinfo();
	}

	public function actionMylist()
	{
		$this->frontPriv();
		return $this->actionListinfo();
	}

    public function actionListinfo($view = null, $dataProvider = null)
    {
		$dataProvider = is_null($dataProvider) ? $this->_getProviderObj() : $dataProvider;

        $view = is_null($view) ? $this->listinfoView : $view;
		if ($this->checkAjax()) {
			$haveModal = $this->getInputParams('have_modal');
			if ($haveModal) {
			  return ['status' => 200, 'content' => $this->renderPartial($view, ['dataProvider' => $dataProvider])];
		    }
			return $this->formatListDatas($dataProvider);
		}

		if (!$this->searchModel->hasProperty('listOperations') && !empty($this->_getListOperations())) {
			$this->searchModel['listOperations'] = $this->_getListOperations();
		}
        $this->setReturnUrl(Yii::$app->request->url);
        return $this->render($view, [
			'searchModel' => $this->searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	protected function _getProviderObj($modelCode = null)
	{
        $this->searchModel = is_null($modelCode) ? $this->model->searchModel : $this->getPointModel($modelCode)->searchModel;
        return $this->searchModel->search($this->getInputParams());
	}

	public function _getListOperations()
	{
		return [];
    }*/
}