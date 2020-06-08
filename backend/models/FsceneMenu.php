<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

class FsceneMenu extends BaseModel
{
	protected function _afterSaveOpe($infert, $changedAttributes)
	{
		return true;
	}

    public function rules()
    {
        return [
            [['menu_code', 'fscene_code'], 'required'],
            ['menu_code', 'checkExist'],
            [['orderlist', 'status'], 'default', 'value' => 0],
            [['path', 'name', 'sort'], 'safe'],
        ];
    }

	public function _beforeSaveOpe($insert)
	{
		if (!empty($this->name)) {
			return true;
		}
		$menu = $this->getPointModel('front-menu')->getInfo($this->menu_code, 'code');
		$this->name = $menu['name'];
		return true;
	}

	public function checkExist()
	{
		$exist = $this->getInfo(['where' => ['menu_code' => $this->menu_code, 'fscene_code' => $this->fscene_code]]);
		if ($exist) {
            $this->addError('fscene_code', '信息已存在');
			return false;
		}
		$menu = $this->getPointModel('front-menu')->getInfo($this->menu_code, 'code');
		$fscene = $this->getPointModel('fscene')->getInfo($this->fscene_code, 'code');
		if (empty($fscene) || empty($menu) || $menu['sort'] != $fscene['sort']) {
            $this->addError('fscene_code', '信息不匹配');
		}
		return false;
	}

    protected function _getTemplatePointFields()
    {
		return [
            'name' => ['type' => 'change', 'formatView' => 'raw', 'width' => '100'],
            'path' => ['type' => 'change', 'formatView' => 'raw', 'width' => '200'],
			'menu_code' => ['type' => 'inline', 'method' => 'formatMenuCode'],
			'fscene_code' => ['type' => 'inline', 'method' => 'formatFsceneCode'],
		];
    }

	public function formatFsceneCode($view = null)
	{
		return $this->fscene_code . ' (' . $this->getPointName('fscene', ['code' => $this->fscene_code]) . ') '; 
	}

	public function formatMenuCode($view = null)
	{
		return $this->menu_code . ' (' . $this->getPointName('front-menu', ['code' => $this->menu_code]) . ') '; 
	}

    public function getGatherListElems()
    {
        return [
			'menu_code' => ['valueType' => 'inline', 'method' => 'formatMenuCode'],
            'name' => ['sort' => 'change'],
            'path' => ['sort' => 'change'],
            'status' => ['sort' => 'change', 'type' => 'dropdown', 'elemInfos' => $this->statusInfos],
            'orderlist' => ['sort' => 'change'],
        ];
    }
}
