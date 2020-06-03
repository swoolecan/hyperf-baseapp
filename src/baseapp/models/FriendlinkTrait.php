<?php

namespace baseapp\models;

trait FriendlinkTrait
{
    public $logo;

    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['orderlist', 'status', 'logo'], 'default', 'value' => 0],
            [['sort', 'pagerank', 'contact', 'mobile', 'qq', 'email', 'wechat', 'description'], 'safe'],
        ];
    }

    public function getSortInfos()
    {
        $datas = [
            'index' => '首页',
            'index-merchant' => '商家官网',
        ];
        return $datas;
    }

    public function _getSingleAttachments()
    {
        return ['logo'];
    }

    protected function _getTemplatePointFields()
    {
        return [
			'logo' => ['type' => 'imgtag'],
			'extFields' => ['logo'],
			'listNo' => [
                'updated_at', 'description', 'email', 'qq',
			],
		];
    }
}
