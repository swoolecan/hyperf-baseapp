<?php

namespace common\controllers\provider;

use Yii;
use yii\helpers\Inflector;

trait TraitRest
{
    public function formatListDatas($datas, $model = null)
    {
        $return = ['status' => 200, 'message' => 'OK'];
        $pointFormat = $this->getInputParams('point_format_list', 'postget');
        $method = '_formatList' .  Inflector::id2camel($pointFormat, '_');
        if (!empty($pointFormat) && method_exists($this, $method)) {
            $withPage = $this->getInputParams('with_page', 'postget');
            if ($withPage) {
                $pagination = is_object($datas) ? $datas->pagination : (isset($datas['pages']) ? $datas['pages'] : []);
                $return['pages'] = $this->model->formatPages($pagination);;
            }
            $return['datas'] = $this->$method($datas, $withPage);
			return $return;
        }
		$model = is_null($model) ? $this->model : $model;
        $return['datas'] = $model->restFormatDatas($datas, $model);
		return $return;
    }

    protected function _formatListCascade($datas, $weithPage = null)
    {
        $return = [];
        $keyField = $this->getInputParams('field_key', 'postget');
		$keyField = empty($keyField) ? 'id' : $keyField;
        $valueField = $this->getInputParams('field_value', 'postget');
		$valueField = empty($valueField) ? 'name' : $valueField;
        foreach ($datas->models as $data) {
            $return[$data[$keyField]] = $data[$valueField];
        }
        return $return;
    }

	protected function _formatListSimple($datas, $weithPage = null)
	{
		$return = [];
        $sourceInfos = is_object($datas) ? $datas->models : (isset($datas['infos']) ? $datas['infos'] : $datas);
		foreach ($sourceInfos as $key => $data) {
            $fields = $data->getSceneFields('simpleList');
			$return[$key] = $data->_restBaseData($fields);
		}
		return $return;
	}

    public function formatDetailData($info, $datas = null)
    {
        $return = ['status' => 200, 'message' => 'OK'];
		if (!is_null($datas)) {
			$return['datas'] = $datas;
			return $return;
		}

        $pointFormat = $this->getInputParams('point_format_view', 'postget');
        $method = '_formatView' .  Inflector::id2camel($pointFormat, '_');
        if (!empty($pointFormat) && method_exists($this, $method)) {
            $return['datas'] = $this->$method($info);
			return $return;
        }
		$return['datas'] = $this->model->restFormatData($info);
		return $return;
    }

	protected function _formatViewSimple($data)
	{
        $fields = $data->getSceneFields('simpleView');
        return $data->_restBaseData($fields);
	}

    /*public function formatResultJson($model, $type)
    {
        $mockMessages = ['OK', '信息有误'];
        $mockStatus = ['200', '400'];
        $isList = in_array($type, ['list', 'listall']) ? true : false;
        $json = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'number', 'description' => '返回状态：200 为发送成功，否则发送失败', 'mock' => ['mock' => $mockStatus[array_rand($mockStatus)]]],
                'message' => ['type' => 'string', 'description' => '返回信息描述', 'mock' => ['mock' => $mockMessages[array_rand($mockMessages)]]],
            ]
        ];

        $datasJson = $model->restDatasJson($type);
        if (!empty($datasJson)) {
            $json['properties']['datas'] = [
                'type' => 'object',
                'description' => '数据信息',
                'properties' => $datasJson,
            ];
        }
        return $json;
	}*/
}
