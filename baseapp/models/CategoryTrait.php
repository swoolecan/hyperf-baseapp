<?php

namespace baseapp\models;

use common\helpers\Tree;
use yii\helpers\ArrayHelper;

trait CategoryTrait
{
	public $thumb;

    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            ['code', 'unique', 'message' => '代码已经被使用。'],
            [['parent_code'], 'filterParent'],
            [['orderlist', 'status'], 'default', 'value' => 0],
			[['type_code', 'parent_code', 'position', 'brief', 'description'], 'safe'],
        ];
    }

    public function filterParent()
    {
        $parent = self::findOne(['code' => $this->parent_code]);
        if (empty($parent)) {
            $this->addError('parent_code', '父分类不存在');
        }
    }

	public function getFormatedInfos()
	{
		$infos = $this->find()->indexBy('code')->asArray()->all();
		//var_dump($infos);exit();
		$formatedInfos = $this->getTreeInfos($infos, 'code', 'parent_code', 'name', '');
		return $formatedInfos;
	}

	public function getSelectInfos()
	{
    	$infos = $this->find()->select(['code', 'name', 'parent_code'])->indexBy('code')->asArray()->all();
		$datas = $this->getLevelDatas($infos, 'code', 'parent_code', 'name', '');
		return $datas;
	}

    protected function _getTemplatePointFields()
    {
        return [
            'type_code' => ['type' => 'key'],
            'position' => ['type' => 'changedown'],
        ];
    }

    public function getPositionInfos()
    {
        return [
            'nav' => '栏目',
            'hot' => '热点',
        ];
    }

	protected function getTypeCodeInfos()
	{
		return [];
	}

	public function getCateInfos()
	{
		$infos = $this->getInfos(['indexBy' => 'code', 'orderBy' => ['orderlist' => SORT_DESC]]);
		foreach ($infos as $key => & $info) {
			$info = $info->formatToArray();
		}
		return $infos;
	}

	public function getGroupInfos()
	{
		$cateInfos = $this->cateInfos;
		$datas = [];
		foreach ($cateInfos as $code => $info) {
			$datas[$info['parent_code']][$code] = $info;
		}
		$infos = $datas[''];
		foreach ($infos as $key => & $data) {
			$data['subInfos'] = isset($datas[$data['code']]) ? $datas[$data['code']] : [];
		}
		return $infos;
	}

	public function formatToArray()
	{
		$thumb = $this->description;
		$info = $this->toArray();
		$info['thumb'] = $thumb;
		return $info;
	}

	public function _sceneFields()
	{
		return [
			'base' => ['id', 'name', 'code'],
		];
	}

	public function getSubDatas($parentCode, $point = false)
	{
		$infos = $this->getInfos(['where' => ['parent_code' => $parentCode], 'indexBy' => 'code']);
		if (empty($point)) {
			return $infos;
		}
        return ArrayHelper::map($infos, 'code', 'name');
	}

	public function getSiblingDatas($point = false)
	{
		return $this->getSubDatas($this->parent_code, $point);
	}
}
