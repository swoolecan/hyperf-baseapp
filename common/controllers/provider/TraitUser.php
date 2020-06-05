<?php
namespace common\controllers\provider;

use Yii;

trait TraitUser
{
	private $_userInfo;

	public function getUserInfo()
	{
		return is_null($this->_userInfo) ? Yii::$app->user->getIdentity() : $this->_userInfo;
	}

	public function setUserInfo($userInfo)
	{
		$this->_userInfo = $userInfo;
	}

	public function getCurrentRole()
	{
		$role = isset($this->userInfo['role']) ? $this->userInfo['role'] : '';
		return $role;
	}

	public function getUserPlat()
	{
		if (empty($this->userInfo)) {
			return [];
		}

		return $this->userInfo->userPlat;
	}

	public function getCurrentAddress($pointId = null)
	{
		$model = $this->getPointModel('user-address');
		if (empty($this->userInfo)) {
			return $model;
		}
		$where = is_null($pointId) ? ['user_id' => $this->userInfo->id] : ['user_id' => $this->userInfo->id, 'id' => $pointId];
		$info = $model->getInfo(['where' => $where, 'orderBy' => ['is_default' => SORT_DESC]]);
		return empty($info) ? $model : $info;
	}

	public function handleUserInfo()
	{
		try {
		    $identity = $this->authenticate(Yii::$app->user, Yii::$app->request, Yii::$app->response);
		} catch (\Exception $e) {
			$identity = null;
		}
		$this->userInfo = $identity;
		return $identity;
	}

	public function getMycartNumber()
	{
		return $this->getPointModel('cart')->myCount($this->userInfo->id);
	}

	protected function _getFsceneDatas($sort)
	{
		$fscenes = $defaultFscene = $myFscenes = $myDefaultFscene = [];
		$infos = $this->getPointModel('fscene')->getInfos(['where' => ['sort' => $sort, 'status' => 1], 'indexBy' => 'code']);
		foreach ($infos as $info) {
			$data = $info->formatToArray();
			$defaultFscene = empty($defaultFscene) ? $data : $defaultFscene;
			if ($info['is_default']) {
				$defaultFscene = $data;
			}
			$fscenes[$info['code']] = $data;
		}
		$myFscenesSource = empty($this->userInfo) ? [] : $this->userInfo->myFscenes;
		foreach ($myFscenesSource as $myFscene) {
			$myCode = $myFscene['fscene_code'];
			$fscene = $fscenes[$myCode];
			$myFscenes[$myCode] = $fscene;
			$myDefaultFscene = empty($myDefaultFscene) ? $fscene : $myDefaultFscene;
			if ($myFscene['is_default']) {
				$myDefaultFscene = $fscene;
			}
		}
		return [
			//'currentFscene' => empty($myDefaultFscene) ? $defaultFscene : $myDefaultFscene,
            'default' => $defaultFscene,
            'myDefaultFscene' => $myDefaultFscene,
			'myFscenes' => $myFscenes,
			'fscenes' => $fscenes,
		];
	}

    public function _dealUserPlat($type, $needMobile = true)
    {
        $data = [];
        $this->handleUserInfo();
        $tModel = $this->getPointModel('user-plat');
        $userPlat = $this->getPointModel('user-plat')->getInfoBySession();
        $data['plat'] = $userPlat;
        if (empty($userPlat)) {
            $data['platStatus'] = 'requestAgain';
            return ['status' => 200, 'message' => 'OK', 'datas' => $data];
        }
        
        $wechats = $this->getPointModel('wechat')->getInfos(['where' => ['sort' => 'wechat', 'status' => 1]]);
        $wechats = $tModel->restSimpleDatas($wechats);
        $return = ['status' => 200, 'message' => 'OK'];
		$needMobile = isset(Yii::$app->params[$type . 'NeedMobile']) ? Yii::$app->params[$type . 'NeedMobile'] : true;
        $userInfo = $userPlat->getUserInfo($type, $needMobile);
        if (empty($userInfo)) {
            if (empty($this->userInfo)) {
                $return['datas'] = ['platStatus' => 'needBind'];
                return $return;
            }
            $r = $userPlat->updateUserId($this->userInfo->id, $type);    
            $return['datas'] = [
                'platStatus' => 'success',
                'token' => Yii::$app->user->identity->getAuthKey(),
                'userInfo' => $this->userInfo->restSimpleData($this->userInfo),
                'userPlats' => $this->userInfo->getUserPlats($this->userInfo),
                'wechats' => $wechats,
            ];
            return $return;
        }

        if (empty($this->userInfo) || !$userPlat->mapUserInfo($this->userInfo->id, $type)) {
            $signinData = $this->getPointModel('entrance-' . $type)->signin($userInfo);
            $return['datas'] = $signinData['datas'];
            return $return;
        }
        $return['datas'] = [
            'platStatus' => 'success',
            'token' => Yii::$app->user->identity->getAuthKey(),
            'userInfo' => $this->userInfo->restSimpleData($this->userInfo),
            'userPlats' => $this->userInfo->getUserPlats($this->userInfo),
            'wechats' => $wechats,
        ];
		return $return;
    }
}
