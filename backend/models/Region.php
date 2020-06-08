<?php

namespace backend\models;

use Yii;
use common\helpers\Tree;
use Overtrue\Pinyin\Pinyin;

class Region extends BaseModel
{
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            ['code', 'unique', 'targetClass' => '\backend\models\Region', 'message' => '代码已经被使用。'],
            [['parent_code'], 'filterParent'],
            [['orderlist'], 'default', 'value' => 0],
            [['parent_code'], 'default', 'value' => ''],
            [['orderlist', 'status', 'spell_one', 'spell', 'spell_short', 'name_full'], 'safe'],
        ];
    }

    public function filterParent()
    {
        $parent = self::findOne(['code' => $this->parent_code]);
        if (empty($parent)) {
            $this->addError('parent_code', '父级区域不存在');
        }
    }

    public function getRegionParent()
    {
        return $this->hasOne(Region::className(), ['code' => 'parent_code']);
    }

    public function getFormatedInfos()
    {
        $infos = $this->find()->indexBy('code')->asArray()->all();
        $formatedInfos = $this->getTreeInfos($infos, 'code', 'parent_code', 'name');
        return $formatedInfos;
    }

    public function getSelectInfos()
    {
        $datas = $this->subInfos('');
        $infos = [];
        foreach ($datas as $data) {
            foreach ($data['sub'] as $info) {
                $infos[$info['code']] = $info;
            }
            unset($data['sub']);
            $infos[$data['code']] = $data;
        }

        $datas = $this->getLevelDatas($infos, 'code', 'parent_code', 'name', '');
        return $datas;
    }

    public function checkInfo($code, $level = 1)
    {
        $info = self::findOne(['code' => $code]);
        if (empty($info)) {
            return ['status' => 400, 'message' => '指定地区不存在'];
        }

        /*if ($info->level < $level) {
            return ['status' => 400, 'message' => '请指定更精确的地区'];
        }*/

        return true;
    }

    public function subInfos($parentCode, $getSub = true)
    {
        $infos = $this->_subInfos($parentCode);
        if ($getSub) {
            foreach ($infos as $key => & $info) {
                $info['sub'] = $this->_subInfos($info['code']);
            }
        }
        return $infos;
    }

    protected function _subInfos($parentCode)
    {
        $infos = self::find()->where(['parent_code' => $parentCode])->asArray()->all();
        return $infos;
    }

    public function getInfoByCode($code)
    {
        static $datas;
        if (isset($datas[$code])) {
            return $datas[$code];
        }

        $info = self::findOne(['code' => $code]);
        $datas[$code] = $info;

        return $info;
    }

    public function isDirect($code)
    {
        $directCodes = ['110000', '120000', '310000', '500000'];
        return in_array($code, $directCodes);
    }

    protected function _getTemplatePointFields()
    {
        return [
			'operation' => ['type' => 'operation'],
			'extFields' => ['operation'],
            'parent_code' => ['type' => 'point', 'table' => 'region', 'pointField' => 'code'],
        ];
    }

	public function getLevelInfos()
	{
		return [
			'province' => '省级',
			'city' => '市级',
			'county' => '区县',
		];
	}

    public function formatOperation($view)
    {
		static $pInfo;
		$str = '';
        if ($this->level != 'province') {
            $menuCodes = [
                'backend_region_listinfo' => ['name' => '所在区域'],
            ];
			$pInfo = is_null($pInfo) ? $this->getInfo($this->parent_code, 'code') : $pInfo;
            $pStr = $this->_formatMenuOperation($view, $menuCodes, ['parent_code' => 'code']);
			$str .= str_replace($this->code, $pInfo['parent_code'], $pStr);
		}
        if ($this->level != 'county') {
            $menuCodes = [
                'backend_region_listinfo' => ['name' => '所辖区域'],
            ];
            $str .= '-' . $this->_formatMenuOperation($view, $menuCodes, ['parent_code' => 'code']);
		}
		return trim($str, '-');
    }

	public function getCodeByName($name, $parentCode)
	{
		$info = $this->getInfo(['where' => ['name' => $name, 'parent_code' => $parentCode]]);
		if (empty($info)) {
		    $nameShort = $this->nameShort($name);
		    $info = $this->getInfo(['where' => ['name_short' => $nameShort, 'parent_code' => $parentCode]]);
		}
		return $info;
	}

	public function nameShort($name)
	{
		if (strlen($name) <= 6) {
			return $name;
		}
		return str_replace($this->nameSuffix(), '', $name);
	}

	protected function nameSuffix()
	{
		return [
            '拉祜族佤族布朗族傣族自治县', '保安族东乡族撒拉族自治县', '彝族哈尼族拉祜族自治县', '傣族拉祜族佤族自治县',
            '哈尼族彝族傣族自治县', '彝族回族苗族自治县', '苗族瑶族傣族自治县', '土家族苗族自治州', '苗族布依族自治县',
            '傣族景颇族自治州', '苗族土家族自治县', '自治区级行政区划', '布依族苗族自治县', '布依族苗族自治州',
            '满族蒙古族自治县', '白族普米族自治县', '土家族苗族自治县', '仡佬族苗族自治县', '独龙族怒族自治县',
            '哈尼族彝族自治县', '蒙古族藏族自治州', '哈尼族彝族自治州', '傣族佤族自治县', '苗族侗族自治州',
            '彝族傣族自治县', '傣族彝族自治县', '回族彝族自治县', '壮族瑶族自治县', '彝族苗族自治县', '黎族苗族自治县',
            '回族土族自治县', '苗族侗族自治县', '彝族回族自治县', '壮族苗族自治州', '达斡尔族自治旗', '藏族羌族自治州',
            '哈萨克族自治县', '维吾尔自治区', '仫佬族自治县', '裕固族自治县', '朝鲜族自治县', '撒拉族自治县', '省级行政区划',
            '蒙古族自治县', '哈尼族自治县', '土家族自治县', '朝鲜族自治州', '拉祜族自治县', '纳西族自治县', '毛南族自治县', '达斡尔族区',
            '羌族自治县', '瑶族自治县', '回族自治县', '回族自治区', '苗族自治县', '壮族自治区', '白族自治州', '土族自治县', '蒙古自治州',
            '佤族自治县', '彝族自治州', '傣族自治州', '藏族自治县', '藏族自治州', '满族自治县', '水族自治县', '黎族自治县', '回族自治州',
            '畲族自治县', '彝族自治县', '侗族自治县', '自治州', '自治县', '自治区', '自治旗', '回族区', '地区', '矿区', '区', '省', '县', '市', '旗',
		];
	}

	public function _sceneFields()
	{
		return [
			'base' => ['id', 'name', 'code'],
		];
	}
}
