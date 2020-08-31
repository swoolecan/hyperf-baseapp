<?php

namespace Swoolecan\Baseapp\Controllers\Traits;

use Swoolecan\Baseapp\Criteria\GreaterCriteria;
use App\Resources\UserCollection;

Trait TraitListinfo
{
    public function listinfo()
    {
        $params = $this->request->all();
        
        $pageSize = $this->request->input('per_page', 15);
        $params = [];
        $repository = $this->getRepositoryObj();
        $criteria = new GreaterCriteria(['field' => 'id', 'equal' => true, 'value' => 3]);
        $repository->pushCriteria($criteria);
        $list = $repository->paginate();

        $collectionClass = $this->getCollectionClass();
        $collection = new $collectionClass($list, 'base', $repository);
        //$collection->setModel($repository->model);
        //$list = $repository->all();
        return $collection->toResponse();
        //$list = $repository->all();//null, $params, (int) $pageSize);
        //$list = $repository->getByCriteria($criteria)->all();
        return $this->success($datas);
    }

    public function tree()
    {
        //$infos = $model->getFormatedInfos();
        $params = $this->request->all();
        $infos = $this->getServiceObj()->all($params);
        $treeParams = $this->_treeParams();
        $treeInfos = $this->getServiceObj()->formatToTree($infos, $treeParams);

        return $this->success($infos);
    }

    protected function _treeParams()
    {
        return [
            'parentField' => 'parent_code',
            'keyField' => 'code',
        ];
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
