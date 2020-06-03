<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\traits\ActiveTrait;

class BaseModel extends ActiveRecord
{
    use ActiveTrait;

    public function attributeLabels()
    {
		$commons = $this->appAttr->getEnvironmentParams('attribute', 'attribute');
		return array_merge(static::_getFieldName(), $commons, $this->attributeExt());
    }

	public function getAttrDatas($table, $sort = null)
	{
		static $datas;
		if (!isset($datas[$table])) {
            $datas[$table] = $this->appAttr->getEnvironmentParams('attribute', $table);
		}
		if (!empty($sort)) {
			return $datas[$table][$sort];
		}
		return $datas[$table];
	}

	protected function attributeExt()
	{
		return [];
	}

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

		if ($this->showUrlcode && empty($this->code)) {
			$this->code = $this->createUrlcode();
		}

		if ($this->fillDescription) {
			$tmp = explode('-', $this->fillDescription);
			$fField = $tmp[0];
			$length = isset($tmp[1]) ? $tmp[1] : 200;

			$this->description = empty($this->description) ? $this->_getSummary($this->$fField) : $this->description;
		}

		return $this->_beforeSaveOpe($insert);
	}

	protected function getFillDescription()
	{
		return false;
	}

	protected function createTagInfo($tags)
	{
		$tags = array_filter(explode(',', $tags));

		$model = $this->getPointModel('tag-info-' . $this->_infocmsCode());
		return $model->createRecord($tags, ['info_type' => $this->shortTable, 'info_id' => $this->id]);
	}

	public function getTagDatas()
	{
		$tagInfos = (array) $this->getPointModel('tag-info-' . $this->_infocmsCode())->getInfos(['where' => ['status' => 1, 'info_type' => $this->shortTable, 'info_id' => $this->id], 'indexBy' => 'tag_code']);
		$tagCodes = array_keys($tagInfos);
		$tInfos = $this->getPointInfos('tag-' . $this->_infocmsCode(), ['where' => ['code' => $tagCodes], 'indexName' => 'code']);
		return $tInfos;
	}

	public function getTagStr()
	{
		$tagDatas = $this->getTagDatas();
		return implode(',', $tagDatas);
	}

	protected function createUrlcode()
	{
		return $this->randomString(8);
	}

	public function getFillTagInfo()
	{
		return false;
	}

	public function getShowUrlcode()
	{
		return false;
	}

	protected function _beforeSaveOpe($insert)
	{
        return true;
    }

	public function afterSave($insert, $changedAttributes)
	{
        parent::afterSave($insert, $changedAttributes);

		if (!empty($this->_getSingleAttachments())) {
		    $this->_updateSingleAttachment($this->_getSingleAttachments());
		}
		if (!empty($this->_getMulAttachments())) {
			foreach ($this->_getMulAttachments() as $mulField) {
		        $this->_updateMulAttachment($mulField);
			}
	    }

		if ($this->fillTagInfo && !empty($this->tags)) {
			$this->createTagInfo($this->tags);
		}
		return $this->_afterSaveOpe($insert, $changedAttributes);
	}

	protected function _afterSaveOpe($infert, $changedAttributes)
	{
		return true;
	}

	public function _getMulAttachments()
	{
		return [];
	}

	public function _getSingleAttachments()
	{
		return [];
	}

	public function getSearchModel()
	{
		$name = $this->className();
		if (strpos($name, '\searchs\\') !== false) {
			return $this;
		}
		$baseName = basename(str_replace('\\', '/', $name));
		$searchClass = str_replace($baseName, "searchs\\{$baseName}", $name);
		$sModel = new $searchClass;
		return $sModel;
	}

	public function getInputParams($code, $type = 'get')
	{
		return Yii::$app->controller->getInputParams($code, $type);
	}

	public function getInputPosts($code, $type = 'get')
	{
		return Yii::$app->controller->getInputPosts($code);
	}

    public function getAppAttr($type = 'controller')
    {
        switch ($type) {
        case 'app':
            return Yii::$app;
        case 'params':
            return Yii::$app->params;
        default:
            return Yii::$app->controller;
        }
    }

    public function getModelCode()
    {
        static $code;
        if (!is_null($code)) {
            return $code;
        }
		$class = $this->className();
        $class = str_replace('\searchs', '', $class);
		$all = $this->appAttr->getRuntimeParams('model');
        $all = array_flip($all);
        $code = $all[$class];
        return $code;
    }
}
