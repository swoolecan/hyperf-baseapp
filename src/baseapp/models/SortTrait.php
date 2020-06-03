<?php

namespace baseapp\models;

trait SortTrait
{
	public $thumb;

    public function rules()
    {
        return [
            [['name'], 'required'],
			[['status'], 'default', 'value' => '0'],
			[['thumb', 'code', 'brief', 'meta_title', 'meta_keyword', 'meta_description', 'description'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
		return array_merge(static::_getFieldName(), [
			'thumb' => '图片',
		]);
    }

	public function afterSave($insert, $changedAttributes)
	{
        parent::afterSave($insert, $changedAttributes);

		$fields = ['thumb'];
		$this->_updateSingleAttachment($fields);

		return true;
	}	

    protected function _getTemplatePointFields()
    {
        return [
            'thumb' => ['type' => 'imgtag'],
            'brief' => ['type' => 'atag'],
			'listNo' => ['description', 'meta_title', 'meta_keyword', 'meta_description'],
        ];
    }
}
