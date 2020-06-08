<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

class Restapi extends BaseModel
{
	use RestapiTrait;
	public $module;

	public function _beforeSaveOpe($insert)
    {
		if ($insert) {
			$count = $this->getInfosCount(['where' => ['menu_code' => $this->menu_code, 'sort' => $this->sort, 'method' => $this->method]]);
			$this->serial = $count + 1;
		}
		if (empty($this->endpoint)) {
			$endpoint = $this->menuData->createUrl('');
			$this->endpoint = "{$this->sort}{$endpoint}";
		}
		$lockFields = ['request', 'response', 'method', 'description', 'endpoint', 'status'];
		if (empty($this->is_lock)) {
			foreach ($lockFields as $field) {
				$lField = "{$field}_lock";
				$this->$lField = $this->$field;
			}
		}
		//$this->initParams($insert);
        return true;
    }

	protected function initParams($insert)
	{
		if ($this->is_lock) {
			return ;
		}
		$forceInit = $this->getInputParams('force_init_params');
		if (!$insert && empty($forceInit)) {
			return ;
		}
	}

	protected function _afterSaveOpe($infert, $changedAttributes)
	{
		return true;
	}

    public function rules()
    {
        return [
            [['menu_code', 'method'], 'required'],
            [['is_lock', 'is_doc', 'status', 'serial'], 'default', 'value' => 0],
            [['request', 'response', 'request_lock', 'response_lock'], 'default', 'value' => ''],
            [['sort', 'name', 'description', 'endpoint', 'method_lock', 'endpoint_lock', 'description_lock', 'status_lock'], 'safe'],
        ];
    }

    protected function _getTemplatePointFields()
    {
        $elems = [
            'method' => ['type' => 'key'],
            'method_lock' => ['type' => 'key'],
            'sort_lock' => ['type' => 'key'],
			'is_lock' => ['type' => 'changedown'],
			'is_doc' => ['type' => 'changedown'],
			'extFields' => ['operation'],
			'listNo' => [
				'request', 'response', 'endpoint', 'description', //'method', 'status'
				'request_lock', 'response_lock', 'endpoint_lock', 'description_lock', //'method_lock', 'status_lock'
		    ],
        ];
        return $elems;
    }

	public function formatOperation($view)
	{
		return $this->visitUrl;
	}

	public function getMethodLockInfos()
	{
		return $this->getMethodInfos();
	}

	public function getMethodInfos()
	{
		return [
			'get' => 'GET',
			'post' => 'POST',
		];
	}

	public function getSortInfos()
	{
		return [
			'' => '前端展示',
			'backend' => '大后台',
			'admin' => '商家后台',
		];
	}

	public function getStatusLockInfos()
	{
		return $this->getStatusInfos();
	}

	public function getStatusInfos()
	{
		return [
		];
	}

	public function getIsLockInfos()
	{
		return [
			0 => '',
			1 => '锁定',
		];
	}

	public function getIsDocInfos()
	{
		return [
			0 => '',
			1 => '描述型接口',
		];
	}

	public function getModuleInfos()
	{
		return $this->getPointModel('base-elem')->getModuleInfos();
	}

	protected function _baksql()
	{
		//ALTER TABLE `wp_restapi` ADD `extfield` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '附加字段' AFTER `updated_at`;
		//UPDATE `wp_restapi` AS `r`, `wp_auth_menu` AS `m` SET `r`.`extfield` = '1' WHERE `r`.`menu_code` = `m`.`code`;
		//UPDATE `wp_restapi` SET `menu_code` = REPLACE(`menu_code`, 'passport_', 'foundation-passport_'), `endpoint` = REPLACE(`endpoint`, 'passport', 'foundation-passport') WHERE `menu_code` LIKE 'passport_%';
	}
}
