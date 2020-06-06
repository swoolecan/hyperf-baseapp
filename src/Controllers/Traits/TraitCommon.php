<?php

namespace Swoolecan\Baseapp\Controllers\Traits;

use Yii;

trait TraitCommon
{
    protected function _getScenario()
    {
        return 'default';
    }

    protected function findModel($id = null, $privType = null, $model = null)
    {
		$model = is_null($model) ? $this->model : $model;
		$id = is_null($id) ? $this->getInputParams('id', 'postget') : $id;
		$data = $model->getInfo($id);
		if (empty($data)) {
			foreach ($this->_getViewFields() as $field) {
				$value = $this->getInputParams($field, 'postget');
				$data = $model->getInfo($value, $field);
				if (!empty($data)) {
					break;
				}
			}
		}
		if (empty($data)) {
			return ['status' => 400, 'message' => '信息不存在', 'model' => $model];
		}
		$privType = is_null($privType) ? $this->action->id : $privType;
        $priv = $data->dealPriv($privType);
		if (empty($priv)) {
			return ['status' => 403, 'message' => '您没有该信息的相关权限', 'model' => $model];
		}
		/*$ownerPriv = $data->dealOwnerPriv($privType);
		if (empty($ownerPriv)) {
			return ['status' => 403, 'message' => '您没有该信息的权限', 'model' => $model];
		}*/
		$this->dealModel($data);
        return ['status' => 200, 'message' => 'OK', 'model' => $data];
    }

	protected function dealModel($model)
	{
	}

    public function getViewPrefix()
    {
		$cMenu = $this->menuInfos['currentMenu'];
		$module = $cMenu['module'];
		if (empty($module)) {
			return "@backend/views/{$cMenu['controller']}/";
		}
		$module = explode('/', $module);
		$module1 = $module[0];
		$module2 = isset($module[1]) ? $module[1] . '/' : '';
        return "@backend/{$module1}/views/{$module2}{$cMenu['controller']}/";
    }

    public function renderForAjax($model)
    {
        return $this->renderPartial('@baseapp/views/change-gather/_ajax', ['model' => $model]);
    }

    public function getMenuUrl($menuCode, $params = [])
    {
        $menus = $this->menuInfos['menus'];
        $appMenus = $this->menuInfos['appMenus'];

        if (isset($appMenus[$menuCode])) {
            $menu = $appMenus[$menuCode];
        } else {
            $menu = isset($menus[$menuCode]) ? $menus[$menuCode] : [];
        }

		if (!isset($menu['url'])) {
			return '';
		}
        $url = $menu['url'];
		if (empty($params)) {
			return $url;
		}
		$queryStr = http_build_query($params);
		$queryStr = strpos($url, '?') !== false ? "&{$queryStr}" : "?{$queryStr}";
		return $url . $queryStr;
    }

	public function getSubnavExt()
	{
		return false;
	}

	public function getSubnavExtLine()
	{
		return false;
	}
}
