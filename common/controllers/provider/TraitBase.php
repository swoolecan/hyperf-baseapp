<?php
namespace common\controllers\provider;

use Yii;
use yii\filters\Cors;
use yii\web\Response;
use common\models\BaseModelNotable;

trait TraitBase
{
	public function getPointModel($code, $forceNew = false, $data = [])
	{
		$model = new BaseModelNotable();
		return $model->getPointModel($code, $forceNew, $data);
	}

	protected function getModel($forceNew = false, $data = [])
	{
		return $this->getPointModel($this->modelCode, $forceNew, $data);
	}

	public function getModelCode()
	{
        static $datas;
		$class = $this->className();
        if (isset($datas[$class])) {
            return $datas[$class];
        }
		$all = $this->getRuntimeParams('model-code');
        $datas[$class] = isset($all[$class]) ? $all[$class] : '';
        return $datas[$class];
		//return $this->id;
	}

	public function getInputParams($code = null, $type = 'get')
	{
		if (Yii::$app->id == 'app-console') {
			return [];
		}

		$gDatas = $type == 'post' ? [] : Yii::$app->request->getQueryParams();
		$pDatas = $type == 'get' ? [] : Yii::$app->request->post();
		$datas = array_merge($gDatas, $pDatas);
		if (is_null($code)) {
			return $datas;
		}

		return isset($datas[$code]) ? $datas[$code] : null;
	}

	public function getInputPosts($code)
	{
		return $this->getInputParams($code, 'post');
	}

	public function getHost()
	{
        return Yii::$app->request->hostInfo;
	}

	public function getClientUrl()
	{
        return Yii::$app->request->url;
	}

    protected function getIsMobile()
    {
        // 通过GET参数，强制设置客户端为移动端，方便调试
        $forceMobile = Yii::$app->getRequest()->get('force_mobile');
        if ($forceMobile) {
            return $forceMobile;
        }

        $detect = new \Mobile_Detect;
        return $detect->isMobile();
    }

	protected function _checkUrl()
	{   
		$url = $this->clientUrl;
        if (strpos($url, 'index.php') !== false) {
            return Yii::$app->response->redirect($this->host)->send();
            exit();
        }

        if (strpos($url, '.html') !== false || strpos($url, '.xml') !== false) {
			return true;
		}

        $pos = strpos($url, '?');
        $query = $pos !== false ? substr($url, $pos) : '';
        $urlBase = str_replace($query, '', $url);
		$lastChar = substr($urlBase, -1);
        if ($lastChar != '/') {
            $rUrl = "{$this->host}{$urlBase}/{$query}";
            return Yii::$app->response->redirect($rUrl, 301)->send();
            exit();
        }
	}

	public function getOriginDomain()
	{
		return Yii::$app->request->origin;
	}

	public function getReferrer()
	{
		$url = Yii::$app->request->referrer;
		$url = strpos($url, Yii::getAlias('@domain-base')) === false ? '' : $url;
		return $url;
	}

    protected function checkAjax()
    {
		if ($this->checkRestApp()) {
		    return true;
		}
        $isAjax = Yii::$app->request->isAjax;
        $isAjax = empty($isAjax) ? $this->getInputParams('force_ajax', 'postget') : $isAjax;
        $isAjax = empty($isAjax) ? Yii::$app->request->post('isCross') : $isAjax;
        if ($isAjax) {
			if (!$this->checkRestApp()) {
                Yii::$app->response->format = Response::FORMAT_JSON; 
			}
            return true;
        }
        return false;
    }

	public function getCurrentMethod()
	{
		return strtolower(Yii::$app->request->method);
	}

	protected function loadDatas($model, $addRelate = false)
	{
		$datas = Yii::$app->request->post();
        $datas = isset($datas[$model->formName()]) ? $datas[$model->formName()] : $datas;
        return $model->load($datas, '');
		/*if ($this->checkRestApp() || $addRelate) {
			return $model->load($datas, '');
		} else {
			return $this->_formName === null ? $model->load($datas) : $model->load($datas, $this->_formName);
        }*/
	}

	protected function _corBehavior()
	{
        header('Access-Control-Allow-Credentials:true');
		//header('Access-Control-Allow-Headers:Authorization');
		//header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control,Authorization");
		$origins = isset(Yii::$app->params['corOrigins']) ? Yii::$app->params['corOrigins'] : [];
		//print_R($origins);exit();
        return [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => $origins,
                'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'OPTIONS'],
				'Access-Control-Request-Headers' => ['*'],
            ],
        ];
	}

	public function getRegionCodeUrl()
	{
		return Yii::getAlias('@restappurl/api-city-code.html', false);
	}

	public function getRuntimeParams($code)
	{
        return \common\helpers\InitFormat::runtimeParams($code);
	}

	public function getEnvironmentParams($sort, $key)
	{
        return \common\helpers\InitFormat::environmentParams($sort, $key);
	}

    protected function _initFields($model, $fields, $preDeal = [], $type = 'post')
    {
        foreach ($fields as $field) {
			if ($type == 'get') {
                $value = Yii::$app->request->get($field, '');
			} else {
                $value = Yii::$app->request->post($field, '');
			}
			$preMethod = isset($preDeal[$field]) ? $preDeal[$field] : false;
			$value = $preMethod ? $preMethod($value) : $value;
            $model->$field = $value;
        }
        return $model;
    }

	public function appendUrlParam($url, $params)
	{
		$url .= strpos($url, '?') !== false ? '&' : '?';
		foreach ($params as $key => $value) {
			$url .= "{$key}={$value}&";
		}
        $url = rtrim($url, '&');
		return $url;
		/*if (isset($params['query'])) {
			$qStr = http_build_query($params['query']);
			$url .= strpos($url, '?') !== false ? '&' . $qStr : '?' . $qStr;
		}
		return $url;*/
	}

    public function copyButton($content, $text = '点我复制')
    {
        return empty($content) ? '' : "<button class='copy-btn' data-clipboard-action='copy' data-clipboard-text='{$content}'>{$text}</button>";
    }

	public function initGlobalDatas()
	{
        return [];
		$globalDatas = $this->getEnvironmentParams('datas', 'front-global-data');
		$key = $this->id . '/' . $this->action->id;
		$globalElems = isset($globalDatas[$key]) ? array_merge(['share'], $globalDatas[$key]) : ['share'];
		foreach ($globalElems as $gKey) {
			$paramKey = $gKey . 'Global';
			$method = $gKey . 'GlobalData';
			Yii::$app->params[$paramKey] = $data = $this->$method();
			if ($paramKey == 'wechat' && !empty($data)) {
				Yii::$app->params['wechatShareInfo'] = $this->wechatShareInfo($data);
			}
		}
		return $globalElems;
	}
}
