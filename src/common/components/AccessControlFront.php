<?php

namespace common\components;

use Yii;
use yii\web\ForbiddenHttpException;
use common\components\AccessControl as AccessControlBase;

class AccessControlFront extends AccessControlBase
{
    public function beforeAction($action)
    {
        $actionId = $action->getUniqueId();
		$actionBase = $action->id;
        $controller = $action->controller;
        foreach ($this->allowActions as $allowAction) {
            if ($allowAction == $actionId) {
				$controller->hasProperty('optional') && $controller->optional = [$action->id];
                return true;
            }
            $allowAction = rtrim($allowAction, "*");
            if (strpos($actionId, $allowAction) === 0) {
				$controller->hasProperty('optional') && $controller->optional = [$action->id];
                return true;
            }
        }
		$guestActions = $controller->getEnvironmentParams('datas', 'front-guest-visit');
		if (in_array($actionId, $guestActions) || in_array($actionBase, ['show', 'list'])) {
			$controller->optional = [$action->id];
		    $controller->ignorePriv = true;
			return true;
		}

        if ($this->user->getIsGuest()) {
			$this->dealGuest();
            return false;
        }
        $this->identity = $this->user->getIdentity();
		$controller->userPrivs = $this->identity->userPrivs;
		$controller->rolePrivs = $this->identity->rolePrivs;

        return true;
    }
}
