<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use baseapp\models\TraitUserAuth;
use yii\web\IdentityInterface;

class Manager extends BaseModel implements IdentityInterface
{
    use TraitUserAuth;

    const STATUS_NOACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 99;

    public $roleType = 'backend';
    public $password_new_repeat;
    public $oldpassword;
    public $password_new;
	public $role_show;

    public static function tableName()
    {
        return '{{%auth_manager}}';
    }

    public function scenarios()
    {
        return [
            'create' => ['name', 'email', 'nickname', 'password_new', 'password_new_repeat', 'status', 'auth_role', 'role_show'],
            'update' => ['name', 'email', 'nickname', 'password_new', 'password_new_repeat', 'status', 'auth_role', 'role_show'],
            'edit-info' => ['email', 'nickname', 'mobile'],
            'edit-password' => ['oldpassword', 'password_new', 'password_new_repeat'],
        ];
    }

    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\backend\models\Manager', 'message' => 'This name has already been taken.'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            [['oldpassword'], 'required'],
            [['oldpassword'], 'checkOldPassword', 'on' => ['edit-password']],
            [['password_new', 'password_new_repeat'], 'required', 'on' => ['create', 'edit-password']],
            ['password_new', 'string', 'min' => 6, 'when' => function($model) { return $model->password_new != ''; }],
            [['password_new_repeat'], 'checkConfirmPassword', 'on' => ['edit-password']],
            //['password_new', 'compare', 'on' => ['edit-password']],
            [['nickname', 'email', 'mobile', 'status', 'role'], 'safe', 'on' => ['create', 'update']],
        ];
    }

    public function checkConfirmPassword()
    {
        if ($this->password_new != $this->password_new_repeat) {
            $this->addError('password_new_repeat', '新密码和确认密码不一致');
        }
    }

    public function checkOldPassword()
    {
        $result = Yii::$app->security->validatePassword($this->oldpassword, $this->getOldAttribute('password'));
        if (!$result) {
            $this->addError('oldpassword', '旧密码错误');
        }
        if ($this->oldpassword == $this->password) {
            $this->addError('oldpassword', '新密码不能跟旧密码相同');
        }

    }

    public function _beforeSaveOPe($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_NOACTIVE;
        }
        if (Yii::$app->controller->id == 'site') {
            return true;
        }
        if (!empty($this->password_new)) {
            $this->setPassword($this->password_new, 'password');
        }
        return true;
    }

    public function _afterSaveOpe($insert, $changedAttributes)
    {
        if (Yii::$app->controller->id == 'entrance' || in_array($this->scenario, ['edit-info', 'edit-password'])) {
            return true;
        }
        $id = $this->attributes['id'];
        $manager = Yii::$app->getAuthManager();
        $manager->revokeAll($this->id);
        foreach ((array) $this->role_show as $roleName) {
            if (empty($roleName)) {
                continue;
            }
            $role = $manager->getRole($roleName);
            $manager->assign($role, $id);
        }

        return true;
    }

    public function getStatusInfos()
    {
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_NOACTIVE => '没激活',
            self::STATUS_LOCK => '锁定',
        ];
    }

    public function getRoleShow()
    {
        $role = ArrayHelper::getColumn(Yii::$app->getAuthManager()->getRolesByUser($this->id), 'name');
        return $role;
    }

    public function getRoleStr()
    {
        return implode(',', (array) $this->getRoleShow());
    }

    public function getRoleInfos()
    {
        $manager = Yii::$app->getAuthManager();
        $roles = $manager->getRoles();

        return array_combine(array_keys($roles), array_keys($roles));
    }

    public function getInfosByRoles($roles)
    {
        $ids = [];
        foreach ($roles as $role) {
            $idsTmp = Yii::$app->getAuthManager()->getUserIdsByRole($role);
            $ids = array_merge($ids, $idsTmp);
        }

        $infos = self::find()->where(['id' => $ids, 'status' => 1])->all();

        return $infos;
    }

    protected function _getTemplatePointFields()
    {
        return [
            'role' => ['type' => 'inline', 'method' => 'getRoleStr'],
			'extFields' => ['role'],
            'listNo' => [
                'last_ip', 'email', 'password', 'auth_key', 'encrypt'
            ],
        ];
    }

    public function getUserPrivs()
    {
        return true;
    }

    public function getRolePrivs()
    {
        return true;
    }
}
