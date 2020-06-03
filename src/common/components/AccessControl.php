<?php

namespace common\components;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\User;
use yii\di\Instance;
use backend\models\Menu;

class AccessControl extends \yii\base\ActionFilter
{
    /**
     * @var User User for check access.
     */
    protected $_user = 'user';

    /**
     * @var array List of action that not need to check access.
     */
    public $allowActions = [];
    protected $identity;

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param User|string $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $controller = $action->controller;
        $actionId = $action->getUniqueId();
		$optionals = [
			'admin/merchant/user/user-plat',
			'admin/merchant/site/fscene',
			'admin/merchant/site/fscene',
		];

		if (in_array($actionId, $optionals)) {
			$controller->hasProperty('optional') && $controller->optional = [$action->id];
		}
        // Allowed actions return true
        foreach ($this->allowActions as $allowAction) {
            if ($allowAction == $actionId) {
                return true;
            }
            $allowAction = rtrim($allowAction, "*");
            if (strpos($actionId, $allowAction) === 0) {
                return true;
            }
        }

        // Whether logined
        if ($this->user->getIsGuest()) {
			$this->dealGuest();
            return false;
        }

        $this->identity = $this->user->getIdentity();
        $checkStatus = $this->_checkStatus();
        if ($checkStatus !== true) {
            throw new ForbiddenHttpException($checkStatus);
        }

        $currentMenu = $this->_checkCurrentMenu($action);
		if ($currentMenu['module'] == 'rest-client') {
            Yii::$app->layout = null;
			return true;
		}
        if (isset($currentMenu['extparam']) && $currentMenu['extparam'] == 'modal') {
            Yii::$app->layout = null;
        }

        $menuInfos = $this->_initMenus($currentMenu);
        $controller->menuInfos = $menuInfos;
		$controller->userPrivs = $this->identity->userPrivs;
		$controller->rolePrivs = $this->identity->rolePrivs;

        //Yii::$app->params['currentMenu'] = $currentMenu;//->toArray();
        //Yii::$app->params['managerInfo'] = $this->identity;

        return true;
    }

    protected function _checkStatus()
    {
        return '您没有执行该操作的权限';
    }

    protected function _checkCurrentMenu($action)
    {
        throw new ForbiddenHttpException(Yii::t('yii', 'error'));
    }

	public function dealGuest()
	{
        $this->user->loginRequired();
	}

	public function getCurrentMenu($action)
	{
        $actionId = $action->getUniqueId();
		if (strpos($actionId, 'admin/') === 0) {
			$actionId = substr($actionId, 6);
		}
		if (strpos($actionId, 'backend/') === 0) {
			$actionId = substr($actionId, 8);
		}

        // Get the current route infos, get the current menu
        $routeData = explode('/', $actionId);
        $currentMethod = array_pop($routeData);
        $currentController = array_pop($routeData);
        $currentModule = implode('/', $routeData);
		$currentModule = empty($currentModule) ? 'backend' : $currentModule;
		$where = ['controller' => $currentController, 'method' => $currentMethod];

		$currentMenu = false;
        $menus = Menu::find()->where($where)->all();//$this->menuModel->getInfo(['where' => $where]);
		foreach ($menus as $menu) {
			if ($menu['module'] == $currentModule) {
				$currentMenu = $menu;
				break;
			}
			if (strpos($menu['module'], '/') !== false && $currentModule == substr($menu['module'], 0, strpos($menu['module'], '/'))) {
				$currentMenu = $menu;
				break;

			}
		}
		return $currentMenu;
	}

	public function getMenuDatas($where, $currentMenu)
	{
        $menus = Menu::find()->where($where)->indexBy('code')->orderBy(['orderlist' => SORT_DESC])->all();
        //$menus = Menu::find()->asArray()->indexBy('code')->all();
        $appMenus = [];
		$baseUrl = $this->baseUrl;
        foreach ($menus as $key => $menu) {
			$url = $menu->createUrl($baseUrl);
			$menu = $menu->toArray();
			$menu['url'] = $url;
            if ($key == $currentMenu['code']) {
                $currentMenu['url'] = $url;
            }
            $menus[$key] = $menu;
            if ($menu['parent_code'] == $currentMenu['parent_code'] && $menu['module'] == $currentMenu['module'] && $currentMenu['controller'] == $menu['controller']) {
                $appMenus[$menu['method']] = $menu;
            }
        }

        $parentMenu = isset($menus['parent_code']) ? $currentMenu['parent_code'] : $currentMenu;
        $menuTitle = $currentMenu['name'];
        $menuBreadCrumb = $currentMenu['name'];
        while (isset($menus[$parentMenu['parent_code']])) {
            $parentMenu = $menus[$parentMenu['parent_code']];
            $menuTitle .= '--' . $parentMenu['name'];
            $menuBreadCrumb = $parentMenu['name'] . '-->' . $menuBreadCrumb;
        }

        $menuInfos = [
            'menuTitle' => $menuTitle,
            'menuBreadCrumb' => $menuBreadCrumb,
            'currentMenu' => $currentMenu,
            'parentMenu' => $parentMenu,
            'appMenus' => $appMenus,
            'menus' => $menus,
        ];
        return $menuInfos;
	}
}
