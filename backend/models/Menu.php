<?php

namespace backend\models;

use Yii;

class Menu extends BaseModel
{
	use MenuTrait;
    public $url;
	public $rest_base;
	public $rest_backend;
	public $rest_admin;

    public static function tableName()
    {
        return '{{%auth_menu}}';
    }

    public function beforeUpdate()
    {
        parent::beforeSave($insert);
        $code = $this->oldAttributes['code'];
        $this->removePermission($code);

        return true;
    }

	public function getRtypeInfos()
	{
		return $this->getPointModel('base-elem')->getRtypeInfos();
	}

    public function getModuleInfos()
    {
		return $this->getPointModel('base-elem')->getModuleInfos();
	}

    public function _afterSaveOpe($insert, $changedAttributes)
    {
        $code = $this->attributes['code'];
        $manager = Yii::$app->getAuthManager();
        $permission = $manager->getPermission($code);
        if ($permission) {
            return true;
        }

        $permission = $manager->createPermission($code);
        $manager->add($permission);

        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $code = $this->attributes['code'];

        $this->removePermission($code);
    }

    protected function removePermission($code)
    {
        $manager = \Yii::$app->getAuthManager();
        $permission = $manager->getPermission($code);
        if ($permission) {
            $manager->remove($permission);
        }

        return ;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            //['code', 'unique', 'targetClass' => '\backend\models\Menu', 'message' => '代码已经被使用。'],
            [['code'], 'checkCode'],
            [['name'], 'required'],
            [['parent_code'], 'filterParent'],
            [['module', 'controller', 'method', 'islog', 'display', 'extparam'], 'safe'],
            [['orderlist'], 'default', 'value' => 0],
        ];
    }

    public function checkCode()
    {
        $code = $this->code;
        $oldCode = $this->getOldAttribute('code');
        if ($code == $oldCode) {
            return ;
        }
        $old = self::find()->where(['code' => $code])->one();
        if (!empty($old)) {
            $this->addError('code', '菜单已存在');
        }
    }

    public function filterParent()
    {
        $value = $this->parent_code;
        $parent = self::findOne(['code' => $value]);
        if (empty($parent)) {
            $this->addError('parent_code', '父菜单不存在');
        }
    }

    /**
     * Get menu parent
     * @return \yii\db\ActiveQuery
     */
    public function getMenuParent()
    {
        return $this->hasOne(Menu::className(), ['code' => 'parent_code']);
    }

    /**
     * Get the infos, format the name to a tree
     *
     */
    public function getFormatedInfos()
    {
		$wheres = [
			'all' => 1,
			'backend' => ['sort' => ['', 'merback']],
			'merchant' => ['sort' => ['merchant', 'merback']],
		];
		$where = $wheres[$this->currentSort];
        $infos = $this->find()->where($where)->indexBy('code')->asArray()->all();
        $formatedInfos = $this->getTreeInfos($infos, 'code', 'parent_code', 'name', '');

        return $formatedInfos;
    }

    /**
     * Get tree list for select
     *
     * @return array
     */
    public function getSelectInfos()
    {
        $infos = $this->find()->select(['code', 'name', 'parent_code'])->indexBy('code')->asArray()->all();
        $datas = $this->getLevelDatas($infos, 'code', 'parent_code', 'name', '');
        return $datas;
    }

    /**
     * Get the islog
     *
     * @return array
     */
    public function getIslogInfos()
    {
        $datas = [
            '0' => '不记录',
            '1' => '记录',
        ];
        return $datas;
    }

    /**
     * Get the display
     *
     * @return array
     */
    public function getDisplayInfos()
    {
        $datas = [
            '1' => '顶部',
            '2' => '左侧',
            '3' => '右侧顶部',
            '4' => '基于记录',
            '99' => '特定位置',
        ];
        return $datas;
    }

	public function getSortInfos()
	{
		return [
			'' => '后台菜单',
			'merchant' => '商家菜单',
			'merback' => '通用菜单',
		];
	}

    protected function _getTemplatePointFields()
    {
        return [
			'rest_base' => ['type' => 'inline', 'method' => 'formatRestapi', 'formatView' => 'raw', 'params' => 'base'],
			'rest_backend' => ['type' => 'inline', 'method' => 'formatRestapi', 'formatView' => 'raw', 'params' => 'backend'],
			'rest_admin' => ['type' => 'inline', 'method' => 'formatRestapi', 'formatView' => 'raw', 'params' => 'admin'],
			'rest_front' => ['type' => 'inline', 'method' => 'formatRestapi', 'formatView' => 'raw', 'params' => 'front'],
			'name' => ['type' => 'inline', 'method' => 'formatName', 'formatView' => 'raw'],
            'module' => ['type' => 'key'],
			'extFields' => ['rest_base', 'rest_backend', 'rest_admin', 'rest_front'],
            'listNo' => [
                'extinfo', 'islog', 'display', 'parent_code', 'controller', 'method', 'orderlist', 'extparam',
            ],
        ];
    }

	public function formatName($view)
	{
		if (!in_array($this->rtype, ['controller', 'model']) || $this->display > 3) {
			return $this->name;
		}

		$merchantUrl = $this->sort != '' ? $this->createUrl(Yii::getAlias('@adminurl', false) . '/admin') : '';
		$baseUrl = $this->sort != 'merchant' ? $this->createUrl() : '';
		$str = empty($baseUrl) ? '' : "<a href='{$baseUrl}' target='_blank'>{$this->name}</a>===";
		$mName = empty($baseUrl) ? $this->name . '(商家Url)' : '(商家Url)';
		$str .= empty($merchantUrl) ? '' : "<a href='{$merchantUrl}' target='_blank'>{$mName}</a>";
		return $str;

	}

	public function formatRestapi($view, $type)
	{
		if (!in_array($this->rtype, ['front', 'controller', 'model'])) {
			return '';
		}
		if ($type == 'base') {
            $menuCodes = [
    			'backend_restapi_add' => ['name' => '添加接口'],
    			'backend_restapi_listinfo' => '',
            ];
            return $this->_formatMenuOperation($view, $menuCodes, ['menu_code' => 'code']);
		}

		$str = '';
		$type = $type == 'front' ? '' : $type;
		$restapis = $this->getPointModel('restapi')->getMenuDatas($this->code, $type);
		foreach ((array) $restapis as $restapi) { 
			$str .= $restapi->visitUrl . '===';
		}

		return trim($str, '===');
	}

	protected function getCurrentSort()
	{
		return Yii::$app->controller->currentSort;
	}
}
