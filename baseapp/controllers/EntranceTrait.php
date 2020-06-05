<?php
namespace baseapp\controllers;

use Yii;
use yii\web\Response;

trait EntranceTrait
{
	protected $beforeData;
	protected $noRender;
	public $authOptional = ['signup', 'signin', 'signupin', 'logout'];
    public $appsort;

	public function beforeAction($action)
	{
		parent::beforeAction($action);
		$actionId = $action->id;
        $this->appsort = $this->getInputParams('appsort');
		if ($actionId == 'logout' || $actionId == 'check-auth') {
			return true;
		}
        if (!empty(Yii::$app->user->returnUrl)) {
            $this->setReturnUrl(Yii::$app->user->returnUrl, 'signin');
        }

        if (!Yii::$app->user->isGuest && $actionId != 'bind' && $actionId != 'fsignin') {
            header('Location: /');exit();
			return $this->goBack();
            //return Yii::$app->response->redirect($this->returnUrl)->send();
        }

		$this->beforeData = $this->beforeData($actionId);
		return true;
	}

	public function actionFsignin()
	{
		$token = $this->getInputParams('token');

        $key = '_merchant_force_signin';

		$tokenInfo = Yii::$app->session->get($key);
        Yii::$app->session->set($key, []);
		if (empty($tokenInfo)) {
		    $cacheKey = $key . $token;
		    $tokenInfo = Yii::$app->cache->get($cacheKey);
            Yii::$app->cache->set($cacheKey, []);
        }
		if (empty($tokenInfo) || $token != $tokenInfo['token']) {
			return $this->returnResult(['status' => 400, 'message' =>'非法请求']);
		}
		if (Yii::$app->params['currentTime'] - $tokenInfo['time'] > 500) {
			//exit('超时');
		}
		$mobile = $tokenInfo['mobile'];
		$result = $this->getModel()->fsignin($mobile);
		$result['pointUrl'] = Yii::getAlias('@adminurl/admin');
        return $this->returnResult($result);
		/*if ($result['status'] != 200) {
            return $this->returnResult($result);
		}
        header('Location: /');*/
	}

	public function actionBind()
	{
		$url = $this->getReturnUrl('signin');
		if (empty($url)) {
		    $url = $this->returnUrl;
		}
		$userPlat = $this->getPointModel('user-plat')->getInfoBySession();
		if (empty($userPlat)) {
			$authUrl = $this->isWechat ? $this->wechatAuthUrl : '/signin-qr.html';
			header("Location: {$authUrl}");
			exit();
		}
		$userInfo = $userPlat->getUserInfo();
		if (empty($userInfo)) {
			if (empty($this->userInfo)) {
		        return $this->render($this->viewPre . 'bind', ['backurl' => '/bind.html']);
			} else {
		        $r = $userPlat->updateUserId($this->userInfo['id']);	
			    header("Location: {$url}");
			}
		} else {
		    if (empty($this->userInfo) || !$userPlat->mapUserInfo($this->userInfo['id'])) {
			    $this->getModel()->signin($userInfo);
			    Yii::$app->session->remove('_session_returnurl');
			}

			header("Location: {$url}");
		}
	}

    public function actionSignin()
    {
		$this->_setTdkTitle('登录');
		return $this->_entrance('signin');
	}

    public function actionSignup()
    {
		$this->_setTdkTitle('注册');
		return $this->_entrance('signup');
	}

    public function actionSignupin()
    {
		$this->_setTdkTitle('注册登录');
		return $this->_entrance('signupin');
	}

	protected function _setTdkTitle($baseTitle)
	{
		if (method_exists($this, 'getSiteElem')) {
            Yii::$app->params['tdkInfo']['title'] = $baseTitle . $this->getSiteElem('seoTitle');
		} else {
            Yii::$app->params['tdkInfo']['title'] = $baseTitle;
		}
	}

	protected function _entrance($action)
	{
		$data = $this->beforeData;
		if ($this->noRender) {
			return $data;
		}
		if ($data['status'] == 200) {
            $returnUrl = $this->getReturnUrl('signin');
            if (!empty($returnUrl)) {
                header("Location: {$returnUrl}");
                exit();
            }
			return $this->goBack();
		}
		$data = is_null($data) ? [] : $data;
        return $this->render($this->viewPre . $action, $data);
    }

    public function actionSigninQr()
    {
		$data = $this->beforeData;
		$data['wxLogin'] = $this->openLoginInfo($this->getOpenLoginParams());
        return $this->render($this->viewPre . 'signin-qr', $data);
    }

    protected function beforeData($action)
    {
		if ($this->checkAjax()) {
			$this->noRender = true;
			Yii::$app->response->format = Response::FORMAT_JSON; 
		}

        $model = $this->getModel();
		$datas = ['userInfo' => [], 'model' => $model, 'return_url' => $this->returnUrl];
        $result = ['status' => 400, 'message' => '', 'datas' => $datas];
        if ($this->isSubmit()) {
			$r = $model->entrance($action);
			if (isset($r['datas'])) {
				$r['datas']['return_url'] = $this->returnUrl;
			}
            $result = array_merge($result, $r);
        }

		return $result;
    }

	public function actionCheckAuth()
	{
		$this->handleUserInfo();
		$name = Yii::$app->request->post('name');
		if (empty($this->userInfo) || $this->userInfo['name'] != $name) {
		    return ['status' => 400, 'message' => '您还没有登录'];
		}
		return ['status' => 200, 'message' => 'OK', 'data' => []];
	}

    public function actionLogout()
    {
        Yii::$app->user->logout();
		if ($this->checkAjax()) {
			return $this->returnResult(['status' => 200, 'message' => '退出成功']);
		}
        return $this->goHome();
    }

	public function actionAuthWechat()
	{
		if ($this->isWechat) {
            $authUrl = $this->wechatAuthUrl;
		} else {
			$openParams = $this->getOpenLoginParams();
			$authUrl = '/signin.html';//$openParams ? '/signin-qr.html' : '/signupin.html';
		}
        header("Location: {$authUrl}");
        exit();
	}

	public function isSubmit()
	{
		return Yii::$app->request->isPost;
	}

    public function getViewPre()
    {
        return '';
    }
}
