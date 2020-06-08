<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

class BaseElem extends BaseModel
{
	use BaseElemTrait;
	public $add_mul;
	public $add_menu;

	public function _beforeSaveOpe($insert)
    {
		$this->name = $this->getFormatCode();
		$this->rtype = $this->createRtype();
        return true;
    }

	protected function _afterSaveOpe($infert, $changedAttributes)
	{
        $this->createMap();
		return true;
	}

    public function rules()
    {
        return [
            [['module', 'code'], 'required'],
            //['code', 'unique', 'targetClass' => '\merchant\models\BaseElem', 'message' => 'This name has already been taken.'],
            [['no_table', 'no_model', 'no_search', 'no_controller', 'rtype'], 'default', 'value' => 0],
            [['full_controller', 'name', 'controller', 'add_menu', 'add_mul', 'model', 'search', 'path', 'path_sub'], 'safe'],
        ];
    }

	protected function attributeExt()
	{
		return [
			'add_mul' => '批量添加 （' . implode('=', $this->addMulFields) . '）',
			'add_menu' => '批量生成菜单 （' . implode('=', $this->addMenuFields) . '）;method: code1|名称1,code2|名称2',
		];
	}

	public function getAddMenuFields()
	{
		return ['parent_code', 'module', 'code', 'method', 'sort'];
	}

	public function getAddMulFields()
	{
		return ['module', 'code', 'full_controller', 'no_table', 'no_model', 'no_search', 'no_controller', 'rtype', 'path', 'path_sub'];
	}

    public function getModuleInfos()
    {
        $moduleAll = $this->appAttr->getEnvironmentParams('attribute', 'module-all');
		$currents = $this->appAttr->getEnvironmentParams('attribute', 'module');
        $datas = [];
        foreach ($currents as $module) {
            $datas[$module] = $moduleAll[$module];
        }
        return $datas;
	}

	public function getNoModelInfos()
	{
		return $this->statusInfos;
	}

	public function getNoSearchInfos()
	{
		return $this->statusInfos;
	}

	public function getNoControllerInfos()
	{
		return $this->statusInfos;
	}

	public function getFullControllerInfos()
	{
		return $this->statusInfos;
	}

	public function getNoTableInfos()
	{
		return $this->statusInfos;
	}

	public function getRtypeInfos()
	{
		return [
			'' => '结构性资源',
			'model' => '模型',
			'controller' => '控制器',
			'front' => '前端控制器',
		];
	}

    protected function _getTemplatePointFields()
    {
        $elems = [
            'module' => ['type' => 'key'],
			'listNo' => ['no_table', 'no_model', 'no_search', 'no_controller', 'path', 'path_sub', 'name'],
        ];
        $full = $this->getInputParams('full');
        if ($full) {
            $elems = array_merge($elems, [
    			'controller' => ['type' => 'inline', 'method' => '_getClass', 'params' => 'controller'],
    			'model' => ['type' => 'inline', 'method' => '_getClass', 'params' => 'model'],
    			'search' => ['type' => 'inline', 'method' => '_getClass', 'params' => 'search'],
    			'checkAction' => ['type' => 'inline', 'formatView' => 'raw', 'method' => '_checkAction'],
    			'extFields' => ['checkAction'],
			]);
        }
        return $elems;
    }

	public function addMenu()
	{
		$datas = explode("\r", $this->add_menu);
		$message = '';
		foreach ($datas as $data) {
			$tmp = trim(str_replace('，', ',', $data));
			$tmp = explode('=', $tmp);
			if (empty(array_filter($tmp))) {
				continue;
			}
			$info = [];
			foreach ($this->addMenuFields as $key => $field) {
				$value = isset($tmp[$key]) ? $tmp[$key] : '';
			    $value = trim(strval($value));
				$info[$field] = $value;
			}
			$add = $this->_addMenu($info);
			if ($add === false) {
				$message .= $data;
			}
		}
		
		return empty($message) ? ['status' => 200, 'message' => 'OK'] : ['status' => '400', 'message' => $message];

	}

	public function createCode()
	{
		return str_replace('/', '-', $this->formatModule) . '_' . $this->getFormatCode('base-format');
	}

	public function createRType()
	{
		$rtype = $this->rtype;
		$rtype = empty($rtype) && empty($this->no_model) ? 'model' : $rtype;
		$rtype = empty($rtype) && empty($this->no_controller) ? 'controller' : $rtype;
		return $rtype;
	}
}
