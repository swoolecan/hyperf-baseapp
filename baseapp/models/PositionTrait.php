<?php

namespace baseapp\models;

trait PositionTrait
{
	public $picture;
	public $picture_mobile;
	public $picture_ext;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['orderlist', 'status'], 'default', 'value' => 0],
            [['owner_mark'], 'default', 'value' => ''],
			[['city', 'sort', 'description', 'url', 'name_ext', 'picture', 'picture_mobile', 'picture_ext'], 'safe'],
        ];
    }

	public function _getSingleAttachments()
	{
		return ['picture', 'picture_mobile', 'picture_ext'];
	}	

	public function getPositionUrl()
	{
		if (empty($this->url)) {
			return 'javascript: void(0);';
		}
		return $this->url;
	}

    protected function _getTemplatePointFields()
    {
        return [
            'owner_mark' => ['type' => 'key'],
            'picture' => ['type' => 'imgtag'],
            'url' => ['type' => 'atag'],
			'status' => ['type' => 'changedown'],
			'extFields' => ['picture'],
			'listNo' => ['description', 'updated_at'],
        ];
    }

	public function getSortInfos()
	{
		return array_merge([
            'slide-pc' => 'PC-首页轮播',
            'slide-mobile' => '移动端-首页轮播',
            'full-pc' => '首页通栏',
		], $this->_extSortInfos());
	}

    protected function _extSortInfos()
    {
        return [];
    }

    public function getOwnerMarkInfos()
    {
        return [];
    }

	public function _sceneFields()
	{
		return [
			'base' => ['id', 'name', 'pictureUrl', 'positionUrl'],
			'ext' => ['pictureUrl', 'positionUrl'],
		];
	}

	public function getCompanyDatas()
	{
		return [];
	}
}
