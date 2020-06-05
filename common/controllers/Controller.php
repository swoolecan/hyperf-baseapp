<?php
namespace common\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller as YiiController;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;

class Controller extends YiiController
{
	use \common\controllers\operation\OperationTrait;
	use \common\controllers\provider\ProviderTrait;
    public $enableCsrfValidation = false;

	public $searchModel;
	public $_formName;

	public $ignorePriv;
	public $userPrivs;
	public $rolePrivs;
	//public $ownerPriv;
	public $menuInfos;

    public $showSubnav = true;
    public $showFilter;
    public $noActionColumn;

	//public $clientType;

    public $pagesysInfo = [];
    public $siteInfoRelate = [];
    public $currentCityCode;
    public $currentCityName;
	protected $_shareData = [];

    public $result = ['errcode' => 404, 'errmsg' => '操作错误'];

    public function actions()
    {
        return [
            'qrcode' => [
                'class' => 'common\actions\QrcodeAction',
            ],
            'spread-record' => [
                'class' => 'common\actions\SpreadRecordAction',
            ],
            'error' => [
                'class' => 'common\actions\ErrorAction',
                'view' => '@views/base/error',
            ],
			'captcha' => [
				'class' => 'common\actions\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
            'wechat-only' => [
                'class' => 'common\actions\WechatOnlyAction',
                'view' => '@views/spage/wechat-only',
            ],
        ];
    }

	public function init()
	{
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->enableCsrfValidation = false;
        }
		if (isset($_GET['access-token']) && empty($_GET['access-token']) && !isset($_GET['force_token']) && $this->checkBackendApp()) {
			$_GET['access-token'] = Yii::$app->params['access-token-test'];
		} elseif (isset($_GET['access-token']) && empty($_GET['access-token']) && !$this->checkBackendApp()) {
			$_GET['access-token'] = Yii::$app->params['access-token-testclient'];
		}
		parent::init();

		if ($this->checkBackendApp()) {
            $this->layout = '@backend/views/charisma/layouts/main';
		}

		$this->_checkUrl();
		if (!empty($this->siteInfo)) {
            //print_r($this->siteInfo);exit();
            $clientSort = $this->siteInfo['client_sort'];
            $client = $this->siteInfo['client'];
			$this->module->viewPath .= $clientSort === 0 ? '' : ($client == 'm' ? '/mobile' : '/pc');
		}
    }

    public function beforeAction($action)
    {
        $this->pagesysInfo['code'] = $this->id . '_' . $action->id;
		if ($this->checkBackendApp()) {
		    $id = $action->id;
		    if ($this->id != 'entrance' && $id != 'error' && $id != 'upload' && !$this->checkMethodValid($id)) {
                throw new NotFoundHttpException('您访问的页面不存在!');
		    }
		}
        return parent::beforeAction($action);
    }

    public function render($view, $params = [])
    {
        $this->formatPagesys();
		//$this->initShareInfo(['title' => $this->getSiteElem('name')]);
        return parent::render($view, $params);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
		if (!is_array($result)) {
			return $result;
		}
		$globalElems = $this->initGlobalDatas();
		foreach ($globalElems as $gKey) {
			$paramKey = $gKey . 'Global';
			$data = $this->appContextDatas($paramKey);
			$result['globalDatas'][$paramKey] = is_object($data) ? $this->_formatViewSimple($data) : $data;
		}

        return $result;
    }

	public function frontPriv($needUser = true, $isView = false)
	{
		$user = $this->userInfo;
		if (empty($user['id']) && $needUser) {
			return ['status' => 400, 'message' => '您还没有登录'];
		}
		if ($needUser) {
		    $_POST['user_id'] = isset($user['id']) ? $user['id'] : 0;
		    $_GET['user_id'] = isset($user['id']) ? $user['id'] : 0;
		}

		$pField = $isView ? 'point_format_view' : 'point_format_list';
		$pFormat = $this->getInputParams($pField);
		$_GET[$pField] = $pFormat == 'full_info' ? null : 'simple';
		$this->ignorePriv = true;
		return true;
	}

	public function checkMethodValid($id)
	{
		if (in_array($id, $this->validActions)) {
			return true;
		}
		$allMethods = $this->getRuntimeParams('method');
		$class = $this->className();
		$methods = isset($allMethods[$class]) ? $allMethods[$class] : [];
		if (in_array($id, $methods)) {
			return true;
		}
		return false;
	}

	public function getValidActions()
	{
		return [];
	}

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = $this->_corBehavior();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'delete' => ['post'],
            ],
        ];

		if (in_array(Yii::$app->id, ['restapp', 'restapp-admin', 'restapp-backend'])) {
            $behaviors['authenticator'] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    QueryParamAuth::className(),
                ],
    			'optional' => isset($this->authOptional) ? $this->authOptional : [],
            ];
		}
		return $behaviors;
	}

	public function checkRestApp($front = false)
	{
		$elems = $front ? ['restapp'] : ['restapp', 'restapp-admin', 'restapp-backend'];
		return in_array($this->currentApp, $elems);
	}

	public function checkBackendApp()
	{
		return in_array($this->currentApp, ['app-backend', 'merchant-admin', 'restapp-admin', 'restapp-backend']);
	}

	public function getCurrentApp($short = false)
	{
		$app = Yii::$app->id;
		return $short ? str_replace('app-', '', $app) : $app;
	}

	public function haveToken()
	{
		return $this->checkRestApp() ? true : false;
	}

    /*protected function getCache($key)
    {
        $infos = \Yii::$app->cacheRedis->get($key);
        return $infos;
    }

    protected function setCache($key, $data)
    {
        \Yii::$app->cacheRedis->set($key, $data);
        return ;
    }*/
}
