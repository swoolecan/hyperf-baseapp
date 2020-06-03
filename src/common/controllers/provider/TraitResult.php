<?php
namespace common\controllers\provider;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

trait TraitResult
{
	protected function returnInfoResult($model, $viewData, $datas = null)
	{
		if ($this->checkAjax()) {
			return $this->formatDetailData($model, $datas);
		}
		$viewData['model'] = $model;
		$view = $viewData['view'];
		unset($viewData['view']);
		return $this->render($view, $viewData);
	}

	protected function returnResult($data, $model = null)
	{
		if ($this->checkAjax()) {
			return $data;
		}

		$pointUrl = isset($data['pointUrl']) ? $data['pointUrl'] : null;
		$pointUrl = is_null($pointUrl) ? $this->returnUrl : $pointUrl;
		if ($this->checkBackendApp() && empty($pointUrl)) {
			$pointUrl = $this->getMenuUrl('listinfo');
			$pointUrl = empty($pointUrl) ? $this->getMenuUrl('listinfo-tree') : $pointUrl;
		}
		$pointUrl = empty($pointUrl) ? Yii::getAlias('@homeurl') : $pointUrl;
		$this->setReturnUrl($pointUrl);

		$status = $data['status'];
		$message = $data['message'];
		if (!empty($data['isTest'])) {
			exit($message);
		}
		switch ($status) {
		case '403':
            throw new ForbiddenHttpException($message);
		    break;
		case '400':
			throw new HttpException($status, $message);
			break;
		case '404':
			throw new NotFoundHttpException($message);
			break;
		default:
			throw new HttpException($status, $message);
		    break;
		}
	}

    public function setReturnUrl($url, $type = 'common')
    {
        $key = '_session_returnurl' . $type;
        Yii::$app->session->set($key, $url);
    }

    public function getReturnUrl($type = 'common')
    {
        $url = $this->getInputParams('return_url', 'postget');
        $key = '_session_returnurl' . $type;
		$url = empty($url) ? Yii::$app->session->get($key) : $url;
        //$url = empty($url) ? Yii::$app->getUser()->getReturnUrl() : $url;
		$url = empty($url) ? $this->currentDomain : $url;
        //$url = empty($url) ? Yii::getAlias('@defaulturl') : $url;
        Yii::$app->session->remove($key);
        return $url;
    }
}
