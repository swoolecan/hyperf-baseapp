<?php

namespace common\models\traits;

use Yii;

trait SearchForm
{
    public function getSearchDatas($returnSimple = false)
    {
        $params = $this->_searchParams() ? $this->_searchParams() : Yii::$app->request->getQueryParams();
        if ($this->load($params, '') && !$this->validate()) {
            return [];
        }

        $datas = $this->_searchDatas($returnSimple);
        if (empty($datas)) {
            return [];
        }
		if (!empty($returnSimple)) {
			$return = [];
			$unsetFields = ['field', 'table', 'where'];
			foreach ($datas as $data) {
				$field = $data['field'];
				foreach ($unsetFields as $unsetField) {
					if (isset($data[$unsetField])) {
						unset($data[$unsetField]);
					}
				}
				$return[$field] = $data;
			}
			return $return;
		}
        return $datas;
    }

    public function _sPointParam($data)
    {
        $data = $this->_sFormatBase($data, 'checkbox');
        if (isset($data['infos'])) {
            return $this->_prefixInfos($data);
        }

        $params = [];
        if (isset($data['where'])) {
            $params['where'] = $data['where'];
        }
        if (isset($data['indexName'])) {
            $params['indexName'] = $data['indexName'];
        }
        if (isset($data['valueName'])) {
            $params['valueName'] = $data['valueName'];
        }
		if (!isset($data['noPriv'])) {
			$params['havePriv'] = true;
	    }
        $data['infos'] = $this->getPointInfos($data['table'], $params);
        return $this->_prefixInfos($data);
    }

    public function _sStartParam($data = [])
    {
        $name = isset($data['name']) ? $data['name'] : '日期时间';
        $field = isset($data['field']) ? $data['field'] : 'created_at';
		$fieldStart = isset($data['noEnd']) ? $field : $field . '_start';
        $format = isset($data['format']) ? $data['format'] : ($field == 'created_at' ? 'YYYYMMDD HH:mm:ss' : 'YYYYMMDD');

        $result = [
            'name' => $name,
            'field' => $fieldStart,
            'type' => 'daytime',
            'format' => $format,
        ];
        if (!isset($data['noEnd'])) {
            $fieldEnd = isset($data['field_end']) ? $data['field_end'] : $field . '_end';
            $formatEnd = isset($data['format_end']) ? $data['format_end'] : ($field == 'created_at' ? 'YYYYMMDD HH:mm:ss' : 'YYYYMMDD');
            $result['end'] = [
                'field' => $fieldEnd,
                'type' => 'daytime',
                'format' => $formatEnd,
            ];
        }
        return $result;
    }

    public function _sHiddenKeyParam($data)
    {
        return $this->_sKeyParam($data, 'hidden');
    }

    public function _sHiddenParam($data)
    {
        return $this->_sFormatBase($data, 'hidden');
    }

	public function _sTextareaParam($data)
	{
        return $this->_sFormatBase($data, 'textarea');
	}

    public function _sTextParam($data)
    {
        return $this->_sFormatBase($data, 'text');
    }

    public function _sButtonParam($data)
    {
        $data['field'] = $data['value'] = 'no';
        return $this->_sFormatBase($data, 'button');
    }

    public function _sKeyParam($data, $type = 'checkbox')
    {
        $data = $this->_sFormatBase($data, $type);
        $data['infos'] = isset($data['infos']) ? $data['infos'] : $this->getKeyInfos($data['field']);
        return $this->_prefixInfos($data);
    }

    protected function _sFormatBase($data, $type)
    {
        //'field' => 'account_id', 
        //'infos' => [], 
        //'option' => [],
        //'wrapClass' => 'hidden',
        //'forceShow' => true, 
        //'ajax' => ['menuCode' => 'spreadaccount_baseinfo_account_listinfo', 'targetField' => 'account_id'],
        $field = $data['field'];
        $data['value'] = isset($data['value']) ? $data['value'] : $this->$field;
        $data['type'] = isset($data['type']) ? $data['type'] : $type;
        $data['name'] = isset($data['name']) ? $data['name'] : $this->getAttributeLabel($data['field']);
        return $data;
    }

    protected function _prefixInfos($data)
    {
        if (count($data['infos']) <= 1 && !isset($data['forceShow'])) {
            //return [];
        }
		/*if (isset($data['priv']) && $data['priv']) {
			return $data;
		}*/
        if (!isset($data['noAll'])) {
            $data['infos'] = ['all-search' => '全部'] + $data['infos'];
        }
        return $data;
    }

    public function _searchDatas($simple = false)
    {
		$sourceMethod = empty($this->searchPointForm) ? '_searchSourceDatas' : $this->searchPointForm;
		$datas = $this->$sourceMethod();
		$default = isset($datas['default']) ? $datas['default'] : [];
		$splits = isset($datas['splits']) ? $datas['splits'] : [];
		$list = isset($datas['list']) ? $this->_fieldFormatSearchDatas('list', $datas['list'], $default, $splits) : [];
		$simples = $list;
		$form = [];
		if (isset($datas['form'])) {
			$formBase = $this->_fieldFormatSearchDatas('form', $datas['form'], $default);
			$form[] = $formBase;
			$simples = array_merge($simples, $formBase);
		}
		if (isset($datas['form1'])) {
			$form1 = $this->_fieldFormatSearchDatas('form', $datas['form1'], $default);
			$form[] = $form1;
			$simples = array_merge($simples, $form1);
		}
		if (isset($datas['form2'])) {
			$form2 = $this->_fieldFormatSearchDatas('form', $datas['form2'], $default);
			$form[] = $form2;
			$simples = array_merge($simples, $form2);
		}
		if (!empty($simple)) {
			return array_filter($simples);
		}
		return [
			'list' => $list,
			'form' => $form,
		];
    }

	protected function _searchSourceDatas()
	{
        return [];
	}

    public function _searchParams()
    {
        return null;
    }
}
