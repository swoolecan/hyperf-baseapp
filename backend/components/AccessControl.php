<?php

namespace backend\components;

use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use backend\models\Manager;
use common\components\AccessControl as AccessControlBase;

class AccessControl extends AccessControlBase
{
    protected function _checkStatus()
    {
        $status = $this->identity->status;
        if ($status == 99) {
            return '账户被锁定，请联系管理员';
        }
        if (empty($status)) {
            return '您的账号还未启用！';
        }
        return true;
    }

    protected function _checkCurrentMenu($action)
    {
		$currentMenu = $this->getCurrentMenu($action);
        if (empty($currentMenu) || !$this->user->can($currentMenu['code'])) {
			$message = empty($currentMenu) ? '操作不存在或您没有执行该操作的权限' : '你没有执行该操作的权限';
            throw new ForbiddenHttpException($message);
        }
		if (!in_array($currentMenu['sort'], ['', 'merback'])) {
            throw new ForbiddenHttpException('您没有执行该操作的权限');
		}
        if ($this->identity->status == Manager::STATUS_NOACTIVE && $currentMenu['controller'] != 'document' && $currentMenu['method'] != 'edit-password') {
            $url = Url::to(['/manager/edit-password']);
            return Yii::$app->response->redirect($url)->send();
        }

        return $currentMenu;
    }

    /**
     * Get the menus of current manager
     */
    protected function _initMenus($currentMenu)
    {
        $manager = Yii::$app->getAuthManager();
        $permissions = $manager->getPermissionsByUser($this->user->id);
        $codes = array_keys($permissions);

		$where = [
			'code' => $codes,
			'sort' => ['', 'merback'],
		];
		return $this->getMenuDatas($where, $currentMenu);
    }

	public function getBaseUrl()
	{
		return Yii::getAlias('@backendurl');
	}

	public function getPrivInfos()
	{
		return true;
	}
}
