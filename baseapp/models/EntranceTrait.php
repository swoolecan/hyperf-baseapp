<?php
namespace baseapp\models;

use Yii;

trait EntranceTrait
{
	protected $postWithForm;
    public $mobile;
    public $nickname;
    public $name;
    public $email;
    public $password;
    public $user_template;
    public $password_confirm;
    public $code;
    public $captcha;
    public $remember_me = 3600 * 24;
    public $scene = '';

    public function scenarios()
    {
        return [
            'signin' => ['name', 'email', 'mobile', 'password', 'captcha'],
            'signup' => ['name', 'email', 'password', 'mobile', 'captcha', 'nickname', 'code'],
            'signupin' => ['mobile', 'captcha', 'code', 'nickname'],
        ];
    }

    public function entrance($action)
    {
        $this->setScenario($action);
        $this->postWithForm ? $this->load(Yii::$app->request->post()) : $this->load(Yii::$app->request->post(), '');
        $validate = $this->validate();
        $result = $validate ? $this->$action() : $this->_formatFailResult('登录失败，请您重试');
		return $result;
    }

    public function getNameField()
    {
        return 'mobile';
    }

	public function fsignin($mobile)
	{
		$userInfo = $this->getUserModel()->getInfo($mobile, 'mobile');
		return $this->signin($userInfo, true);
	}

    public function signin($userInfo = null, $force = false)
    {
        $userInfo = is_null($userInfo) ? $this->getUserInfo() : $userInfo;
        $loginResult = Yii::$app->user->login($userInfo, $this->remember_me);
        if (!$loginResult) {
            return ['status' => 400, 'message' => '登录失败'];
        }
		if (empty($force)) {
		    $userInfo = $userInfo->dealSignin();
		}

        $this->wrongTimes('clear');
		$token = Yii::$app->controller->haveToken() ? Yii::$app->user->identity->getAuthKey() : '';
		/*$userPlat = $this->getPointModel('user-plat')->getInfoBySession();
		if (!empty($userPlat)) {
			$this->updateUserPlat($userPlat, $userInfo);
		}*/
		$wechats = $this->getPointModel('wechat')->getInfos(['where' => ['sort' => 'wechat', 'status' => 1]]);
		$datas = [
			'token' => $token, 
			'userInfo' => $userInfo->restSimpleData($userInfo), 
			'userPlats' => $userInfo->getUserPlats($userInfo),
			'wechats' => $this->restSimpleDatas($wechats),
		];
        return ['status' => 200, 'message' => 'OK', 'datas' => $datas];
    }

    public function signupin()
    {
        $userInfo = $this->getUserInfo();
        if (empty($userInfo)) {
            $result = $this->signup(true);
            if (!$this->isResultOk($result)) {
                return $result;
            }
            $userInfo = $result['userInfo'];
        }
		if (!empty($this->nickname) && $this->nickname != $userInfo['nickname']) {
			$userInfo->nickname = $this->nickname;
			$userInfo->update(false, ['nickname']);
		}
		return $this->signin($userInfo);
    }

    public function signup($emptyPassword = false)
    {
		$data['password_empty'] = intval($emptyPassword);
		foreach (['mobile', 'password', 'name', 'nickname'] as $field) {
			$data[$field] = (string) strip_tags(trim($this->$field));
        }

        $model = $this->getUserModel();
        $result = $model->register($data);
        if (empty($result)) {
            return ['status' => 400, 'message' => '注册失败！'];
        }
        return ['status' => 200, 'message' => 'OK', 'userInfo' => $this->getUserInfo()];
    }

    public function wrongTimes($action) 
    {
        $session = Yii::$app->getSession();
        $session->open();
        $name = "_login_count";

        switch ($action) {
        case 'write':
            $count = isset($session[$name]) ? $session[$name] + 1: 1;
            $session[$name] = $count;
            return ;
        case 'check':
            $count = isset($session[$name]) ? $session[$name] : 0;
            return $count;
        case 'clear':
            if (isset($session[$name])) {
                unset($session[$name]);
            }
            return ;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        $user = $this->getUserInfo();
		if (!$user) {
            $this->addError($this->nameField, '用户不存在');
			return ;
		}

        if (!$user->validatePassword($this->password)) {
			$this->wrongTimes('write');
            $this->addError('password', '密码错误');
        }
    }
}
