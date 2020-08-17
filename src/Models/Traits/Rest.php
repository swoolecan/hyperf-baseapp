<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Models\Traits;

trait Rest
{
    public function restFormatDatas($datas, $model, $elems = ['pages', 'attributes', 'searchAttributes'])
    {
        $return = [];
        $sourceInfos = is_object($datas) ? $datas->models : (isset($datas['infos']) ? $datas['infos'] : $datas);
        $infos = [];
        foreach ($sourceInfos as $key => $info) {
            $fields = array_keys($info->_restFieldsFormat('list'));
            $infos[$key]['base'] = $info->_restBaseData($fields);
            $infos[$key]['relateData'] = $info->_restRelateDatas();
        }
        $return['infos'] = $infos;

        if (in_array('attributes', $elems)) {
            $relateAttributes = $model->relateAttributes;
            $return['relateAttributes'] = $relateAttributes;
        }
        if (in_array('searchAttributes', $elems)) {
            $searchAttributes = $model->searchAttributes;
            $return['listSearchAttributes'] = $searchAttributes;
        }

        if (in_array('pages', $elems)) {
            $pagination = is_object($datas) ? $datas->pagination : (isset($datas['pages']) ? $datas['pages'] : []);
            $return['pages'] = $model->formatPages($pagination);;
        }
        $return['baseFields'] = $model->_restFieldsFormat('show');
        return $return;
    }

	public function restSimpleDatas($infos, $sort = 'simpleView')
	{
		$datas = [];
		foreach ($infos as $key => $info) {
			$datas[$key] = $this->restSimpleData($info, $sort);
		}
		return $datas;
	}

	public function restSimpleData($info, $sort = 'simpleView')
	{
        $fields = $info->getSceneFields($sort);
        return $info->_restBaseData($fields);
	}

    public function restFormatData($info, $elems = ['base', 'form', 'attributes'])
    {
        $fields = array_keys($this->_restFieldsFormat('show'));
        $return['info'] = ['base' => $info->_restBaseData($fields), 'relateData' => $info->_restRelateDatas()];

        if (in_array('attributes', $elems)) {
            $relateAttributes = $info->relateAttributes;
            $return['relateAttributes'] = $relateAttributes;
        }
        if (in_array('base', $elems)) {
            $return['baseFields'] = $this->_restFieldsFormat('show');
		}
        if (in_array('form', $elems)) {
            $return['formFields'] = $info->_dealFormFields($this->_restFormFields($return['baseFields']));
		}
        return $return;
    }

    public function _restRelateDatas()
    {
        $infos = $this->_relateDataInfos();
        $return = [];
        foreach ($infos as $rKey => $rValue) {
            $elem = is_array($rValue) ? $rKey : $rValue;
            $elemName = "{$elem}Data";
            $data = $this->$elemName;
            $fields = is_array($rValue) ? $rValue : $data->getSceneFields('relate');

            $return[$elemName] = $data->_restBaseData($fields);
        }
        return $return;
    }

    public function _restBaseData($fields)
    {
		$fields = !is_array($fields) ? $this->getSceneFields($fields) : $fields;
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $this->$field;
        }
        return $data;
    }

    public function _restFieldsFormat($sort)
    {
        $fields = $this->_getFieldName();
        $exts = $this->getSceneFields('ext');
        foreach ($exts as $ext) {
            $fields[$ext] = $this->getAttributeLabel($ext);
        }

		$sortFields = (array) $this->getSceneFields($sort);
        $nos = $this->getSceneFields("{$sort}No");
        foreach ($nos as $no) {
			if (!in_array($no, $sortFields)) {
                unset($fields[$no]);
			}
        }
        return $fields;
    }

    public function _restFormFields($baseFields)
    {
        $sorts = [
            'timestamp' => '时间戳',
            'date' => '日期',
            'datetime' => '日期时间',
            'text' => '文本',
            'picture' => '单图',
            'picture-mul' => '多图',
            'key' => '键值对',
            'point' => '关联数据',
        ];
        $fields = $this->getSceneFields('form');
        foreach ($fields as $field => & $fInfo) {
            $fInfo['name'] = isset($fInfo['name']) ? $fInfo['name'] : (isset($baseFields[$field]) ? $baseFields[$field] : '');
            $brief = '';
            if (isset($fInfo['require'])) {
                $brief .= '必填-=-';
            }
			$fInfo['sort'] = !isset($fInfo['sort']) ? 'text' : $fInfo['sort'];
            $brief .= isset($sorts[$fInfo['sort']]) ? $sorts[$fInfo['sort']] : $fInfo['sort'];
            $fInfo['brief'] = isset($fInfo['brief']) ? $brief . '-=-' . $fInfo['brief'] : $brief;
        }
        return $fields;
    }

    public function formatPages($pages)
    {
        return [
            'totalCount' => $pages->totalCount,
            'pageSize' => $pages->pageSize,
            'page' => $pages->getPage() + 1,
        ];
    }

    public function getSceneFields($sort)
    {
        $baseDefault = ['id'];
        if ($this->hasProperty('name')) {
            $baseDefault[] = 'name';
        }
        $defaults = [
            'base' => $baseDefault,
            'listNo' => [],
            'showNo' => [],
            'form' => [],
            'ext' => [],
        ];
        $datas = array_merge($defaults, $this->_sceneFields());
        $datas['relate'] = isset($datas['relate']) ? $datas['relate'] : $datas['base'];
        if (isset($datas[$sort])) {
            return $datas[$sort];
        }
        return isset($datas['base']) ? $datas['base'] : [];
    }

    public function _relateDataInfos()
    {
        return [];
    }

    public function _sceneFields()
    {
        return [];
    }

	public function _dealFormFields($fields)
	{
		return $fields;
	}
}
