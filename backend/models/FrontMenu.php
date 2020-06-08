<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

class FrontMenu extends BaseModel
{
	protected function _afterSaveOpe($infert, $changedAttributes)
	{
		return true;
	}

    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            ['code', 'unique', 'targetClass' => '\backend\models\FrontMenu', 'message' => 'This name has already been taken.'],
            [['status'], 'default', 'value' => 0],
            [['icon', 'sort'], 'safe'],
        ];
    }

	public function getSortInfos()
	{
		return $this->getPointModel('fscene')->getSortInfos();
	}

	public function getIconInfos()
	{
		return [
			'wap-home' => '主页',
			'scan' => '扫描',
			'setting-o' => '设置',
			'shopping-cart-o' => '购物车',
			'friends-o' => '人们',
			'manager-o' => '管理员',
			'orders-o' => '列表',
			'apps-o' => '应用',
			'user-o' => '用户中心',
			'contact' => '联系人',
			'service-o' => '客服',
			'photo-o' => '图片',
			'qr' => '二维码',
			'shop-o' => '店铺',
			'comment-o' => '评论/申请',
			'phone-o' => '电话',
		];
	}

    protected function _getTemplatePointFields()
    {
        $elems = [
			'icon' => ['type' => 'changedown'],
        ];
        return $elems;
    }

	public function formatOperation($view)
	{
		return $this->visitUrl;
	}
}
