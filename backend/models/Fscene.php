<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

class Fscene extends BaseModel
{
	protected function _afterSaveOpe($infert, $changedAttributes)
	{
		return true;
	}

    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            ['code', 'unique', 'targetClass' => '\backend\models\Fscene', 'message' => 'This name has already been taken.'],
            [['status'], 'default', 'value' => 0],
            [['sort'], 'safe'],
        ];
    }

	public function getSortInfos()
	{
		return [
			'passport' => 'C端用户',
			'merchant' => '商家用户',
		];
	}

    protected function _getTemplatePointFields()
    {
		return [
            'menuNum' => ['type' => 'inline', 'method' => 'getMenuNum', 'formatView' => 'raw'],
			'extFields' => ['menuNum'],
        ];
    }

	public function getFsceneMenuDatas($all = false)
	{
		$where = empty($all) ? ['fscene_code' => $this->code, 'status' => 1] : ['fscene_code' => $this->code];
		return $this->getPointModel('fscene-menu')->getInfos(['where' => $where, 'orderBy' => ['orderlist' => SORT_DESC]]);
	}

	public function getMenuNum($view)
	{
        $num = count($this->fsceneMenuDatas);
        $menuCodes = [
            'backend_fscene_update' => ['name' => '菜单数 ' . $num, 'qStr' => 'update_relate=1'],
        ];
        return $this->_formatMenuOperation($view, $menuCodes, ['id' => 'id'], ['target' => '_blank']);
	}

	public function formatToArray()
	{
		$return = $this->toArray();
		$menuDatas = $this->fsceneMenuDatas;
		$menus = [];
		foreach ($menuDatas as $data) {
			if (empty($data['status'])) {
				continue;
			}
			$menu = $this->getPointModel('front-menu')->getInfo($data['menu_code'], 'code');
			$menus[$data['menu_code']] = array_merge($menu->toArray(), $data->toArray());
		}
		$return['menus'] = $menus;
		return $return;
	}
}
