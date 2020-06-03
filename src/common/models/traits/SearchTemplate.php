<?php

namespace common\models\traits;

use Yii;
use yii\helpers\Html;
use common\widgets\FileUploadUI;
use kartik\select2\Select2;
use yii\web\JsExpression;

trait SearchTemplate
{
	public function formatTemplateDatas($sort = 'list', $view = null, $fields = null)
	{
		$fields = is_null($fields) ? $this->_getTemplateFields() : $fields;
		$datas = [];
		foreach ($fields as $field => $info) {
			if (isset($info[$sort . 'No'])) {
				continue;
			}
			if ($sort == 'show' && $info['type'] == 'checkbox') {
				continue;
			}
	
			/*if (!isset($info['type'])) {
				$datas[$field] = $info;
				continue;
			}*/
			$type = isset($info['type']) ? $info['type'] : 'common';
			if ($type == 'checkbox') {
            $data = [
                'class'=>\yii\grid\CheckboxColumn::className(),
                'checkboxOptions' => function ($model, $key, $index, $column) {
                     return ['value'=>$model->id,'class'=>'checkbox'];
                }
            ];

			} else {
			$method = "_{$type}Template";
			$data = [
				'attribute' => $field,
				'value' => $this->$method($field, $info, $sort, $view),
			];
            if (isset($info['formatView']) || in_array($type, ['atag', 'imgtag', 'operation', 'changedown', 'change'])) {
                $data['format'] = isset($info['formatView']) ? $info['formatView'] : 'raw';
            }
			}
			$datas[$field] = $data;
		}
		return $datas;
	}

	protected function _atagTemplate($field, $info, $sort, $view)
	{
		if ($sort == 'list') {
    		$value = function($model) use ($field, $info) {
				return $model->formatAtag($field, $info);
            };
		} else {
			$value = $this->formatAtag($field, $info);
		}
		return $value;
	}

	protected function _imgtagTemplate($field, $info, $sort, $view)
	{
		if ($sort == 'list') {
    		$value = function($model) use ($field, $info) {
				return $model->formatImgtag($field, $info);
            };
		} else {
			$value = $this->formatImgtag($field, $info);
		}
		return $value;
	}

	protected function _checkboxTemplate($field, $info, $sort, $view)
	{
    	$value = function($model) use ($field) {
			return $model->$field;
        };
		return $value;
	}

	protected function _commonTemplate($field, $info, $sort, $view)
	{
		if ($sort == 'list') {
    		$value = function($model) use ($field) {
				return $model->$field;
            };
		} else {
			$value = $this->$field;
		}
		return $value;
	}

	protected function _pointTemplate($field, $info, $sort, $view)
	{
		$table = $info['table'];
		$pointField = isset($info['pointField']) ? $info['pointField'] : 'id';
		$pointName = isset($info['pointName']) ? $info['pointName'] : 'name';
		if ($sort == 'list') {
    		$value = function($model) use ($field, $table, $pointField, $pointName, $info) {
                return $model->getPointName($table, [$pointField => $model->getRelateField($field)], $pointName);
            };
		} else {
			$value = $this->getPointName($table, [$pointField => $this->$field], $pointName);
		}
		return $value;
	}

	protected function _conditionTemplate($field, $info, $sort, $view)
	{
        $method = "_conditionElem";
		if ($sort == 'list') {
    		$value = function($model) use ($field, $method, $view) {
                return $model->$method($field, $view);
                //return $model->getKeyName($field, $model->$field);
            };
		} else {
            $value = $this->$method($field, $view);
		}
		return $value;
	}

	protected function _keyTemplate($field, $info, $sort, $view)
	{
		if ($sort == 'list') {
    		$value = function($model) use ($field) {
                return $model->getKeyName($field, $model->getRelateField($field));
            };
		} else {
            $value = $this->getKeyName($field, $this->$field);
		}
		return $value;
	}

	protected function _timestampTemplate($field, $info, $sort, $view)
	{
		if ($sort == 'list') {
    		$value = function($model) use ($field) {
                return $model->formatTimestamp($model->$field);
            };
		} else {
            $value = $this->formatTimestamp($this->$field);
		}
		return $value;
	}

    protected function _changedownTemplate($field, $info, $sort, $view)
    {
        if ($sort != 'list') {
            return $this->$field;
        }
        $menuCode = isset($info['menuCode']) ? $info['menuCode'] : '';
        $menu = $menuCode ? $view->getMenuData($menuCode) : $view->getMenuApp('update');
        if (empty($menu)) {
            return $this->$field;
        }
        $menuUrl = $menu['url'];
        $info['sort'] = 'change';
        $info['type'] = isset($info['typeView']) ? $info['typeView'] : 'dropdown';
		$info['menuCode'] = $menu['code'];
		$info['elemInfos'] = isset($info['elemInfos']) ? $info['elemInfos'] : $this->getKeyInfos($field);
        $info['noWrap'] = true;
        return function ($model) use ($field, $info, $view) {
			if (is_string($info['elemInfos']) && $info['elemInfos'] == 'point') {
				$info['elemInfos'] = $model->changedownElems($field);
			}
            return $view->getElemView($model, $field, $info);
        };
    }

    protected function _changeTemplate($field, $info, $sort, $view)
    {
        if ($sort != 'list') {
            return $this->$field;
        }
        $menuCode = isset($info['menuCode']) ? $info['menuCode'] : '';
        $menu = $menuCode ? $view->getMenuData($menuCode) : $view->getMenuApp('update');
        if (empty($menu)) {
            return $this->$field;
        }
        $menuUrl = $menu['url'];
        $info['sort'] = 'change';
        $info['type'] = isset($info['typeView']) ? $info['typeView'] : 'common';
		$info['menuCode'] = $menu['code'];
        $info['noWrap'] = true;
        return function ($model) use ($field, $info, $view) {
            return $view->getElemView($model, $field, $info);
        };
    }

	protected function _inlineTemplate($field, $info, $sort, $view)
	{
		$method = $info['method'];
		$params = isset($info['params']) ? $info['params'] : null;
		if ($sort == 'list') {
    		$value = function($model) use ($field, $method, $view, $params) {
                return empty($params) ? $model->$method($view) : $model->$method($view, $params);
            };
		} else {
            $value = empty($params) ? $this->$method($view) : $this->$method($view, $params);
		}
		return $value;
	}

	protected function _operationTemplate($field, $info, $sort, $view)
	{
		$method = isset($info['method']) ? $info['method'] : 'formatOperation';
		if ($sort == 'list') {
    		$value = function($model) use ($field, $method, $view) {
                return $model->$method($view);
            };
		} else {
            $value = $this->$method($view);
		}
		return $value;
	}

    protected function _formatMenuOperation($view, $menuCodes, $params, $extParams = [])
    {
        $str = '';
		$target = isset($extParams['target']) ? $extParams['target'] : '';
		$seperator = isset($extParams['seperator']) ? $extParams['seperator'] : '---';

        foreach ($menuCodes as $code => $mInfo) {
			$code = strpos($code, '__rand') !== false ? substr($code, 0, strpos($code, '__rand')) : $code;
			$mInfo = (array) $mInfo;
            $menu = $view->getMenuData($code);
            if (empty($menu)) {
                continue;
            }
    		/*$check = $this->checkBackendPriv($code);
			if (empty($check)) {
				continue;
			}*/
            $name = isset($mInfo['name']) ? $mInfo['name'] : $menu['name'];
            $qStr = isset($mInfo['qStr']) ? $mInfo['qStr'] : '';
            foreach ($params as $param => $field) {
                $value = $this->$field;
                $qStr .= "&{$param}={$value}&";
            }
			foreach ($extParams as $eParam => $vField) {
				if (in_array($eParam, ['target', 'seperator'])) {
					continue;
				}
				$vValue = $view->params[$vField];
				$qStr .= "&{$eParam}={$vValue}&";
			}
			$qStr = trim($qStr, '&');
            $url = $menu['url'] . "?{$qStr}";
			$isModal = isset($mInfo['isModal']) ? $mInfo['isModal'] : false;
			$url = $isModal ? $this->_getModalUrl($url, $name) : "<a href='{$url}' target='{$target}'>{$name}</a>";
            $str .= "{$url}{$seperator}";
        }
        $str = rtrim($str, $seperator);
        return $str;
    }

	public function _getModalUrl($url, $name)
	{
		$url = 'javascript:showModalPage("' . $url . '");';
        $str = "<a data-pjax='0' aria-label='{$name}' title='{$name}' href='{$url}' class='btn-setting-log'>{$name}</a>";
		return $str;
	}

    protected function _operationOldTemplate($field, $info, $sort, $view)
    {
        $queryStr = '';
        if (isset($info['qParam'])) {
            foreach ($info['qParam'] as $field => $qParam) {
                $qField = $qParam['field'];
                $value = is_null($qParam['value']) ? $this->$qField : $qParam['value'];
                $queryStr .= "{$field}={$value}&";
            }
        }
        $urlStr = '';
        $menus = $info['menuCodes'];
        foreach ($menus as $key => & $data) {
            $code = $data['code'];
            $menu = $view->getMenuData($code);
            if (empty($menu)) {
                unset($menus[$key]);
                continue;
            }
            $data['url'] = $menu['url'];
            $data['name'] = isset($data['name']) ? $data['name'] : $menu['name'];
            $qParams = isset($info['qParams']) ? $info['qParams'] : [];
            $qParams1 = isset($data['qParams']) ? $data['qParams'] : [];
            $data['qParams'] = array_merge($qParams, $qParams1);
        }

        //$urlStr .= "<a href='{$url}'>{$name}</a>---";
        //$urlStr = rtrim($urlStr, '---');
        //print_r($menus);exit();

		if ($sort == 'list') {
    		$value = function($model) use ($menus) {
                $urlStr = '';
                foreach ($menus as $menu) {
                    if (isset($menu['condition'])) {
                        $continue = false;
                        foreach ($menu['condition'] as $cField => $cValue) {
                            if ($model->$cField != $cValue) {
                                $continue = true;
                                continue;
                            }
                        }
                        if ($continue) {
                            continue;
                        }
                    }

                    $queryStr = '';
                    foreach ($menu['qParams'] as $field => $qParam) {
                        $qField = $qParam['field'];
                        $value = is_null($qParam['value']) ? $model->$qField : $qParam['value'];
                        $queryStr .= "{$field}={$value}&";
                    }
                    $url = $menu['url'] . '?' . $queryStr;
                    $urlStr .= "<a href='{$url}'>{$menu['name']}</a>---";
                }
                return rtrim($urlStr, '---');
            };
		} else {
            $value = $urlStr;
		}
		return $value;

    }

	public function _getListOperations($opes, $exts = [])
	{
		$operations = [
			'delete' => [
				'name' => '删除选中项',
				'confirm' => '确定要删除选中的信息吗',
				'mCode' => 'delete',
			],
		];
		$return = [];
		foreach ((array) $opes as $ope) {
			$return[$ope] = $operations[$ope];
		}
		$return = array_merge($return, $exts);
		return $return;
	}

	public function cascadeElem($urlOrCode, $subId, $field, $options = [])
	{
		$url = strpos($urlOrCode, 'http') !== false ? $urlOrCode : Yii::$app->controller->getMenuUrl($urlOrCode);
		$url .= strpos($url, '?') !== false ? '&' : '?';
	   	$url .= 'point_format_list=cascade';
		$base = ['field_key' => 'id', 'field_value' => 'name'];
		$options = array_merge($base, $options);
		foreach ($options as $key => $value) {
			$url .= "&{$key}={$value}";
		}
        return '$.get("' . $url . '&' . $field . '="+$(this).val(),function(data){
            var htmlContent = "<option value=\"\"></option>";
            $.each(data.datas, function(i, v) {
                htmlContent += "<option value=\"" + i + "\">" + v + "</option>";
            });
            $("#' . $subId . '").html(htmlContent);
        });';
	}

	public function datetimeForm($fieldId)
	{
		return $this->dateForm($fieldId, 'YYYY-MM-DD HH:mm:ss');
	}

	public function dateForm($fieldId, $format = 'YYYYMMDD')
	{
		return '<script type="text/javascript">'
            . '$(function () {'
            . "$('#{$fieldId}').datetimepicker({locale: 'zh-CN', format: '{$format}', showClear:true});"
            . '});</script>';
	}

    public function uploadElem($field, $accept = 'image/*')
    {
        $attachment = $this->attachmentModel;
        $fieldElem = $attachment->getFieldInfos($this->shortTable, $field);
        return FileUploadUI::widget([
            'model' => $attachment,
            'attribute' => "files[{$field}]",
            'url' => $this->uploadUrl($this->attachmentMark, $this->shortTable, $field, $this->id), 
            'gallery' => true,
            'fieldOptions' => [
                'isSingle' => $fieldElem['isSingle'],
                'idField' => Html::getInputId($this, $field),
                'accept' => $accept
            ],
            'clientOptions' => [
                //'dataType' => 'json',
                'maxFileSize' => $fieldElem['maxSize'] * 1024,
            ],
        ]);
    }

	public function selectElem($form, $model, $field, $url)
	{
	   	$url .= '?point_format_list=cascade&force_ajax=1';
        //$form->field($model, 'brand_code')->dropDownList($brandInfos, ['prompt' => '']);
        echo $form->field($model, $field)->widget(Select2::classname(), [
            'options' => ['placeholder' => '请输入标题名称 ...'],
            'pluginOptions' => [
                'placeholder' => 'search ...',
                'allowClear' => true,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting...'; }"),
                ],
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(res) { return res.datas }'),
                'templateSelection' => new JsExpression('function (res) { return res.datas; }'),
            ],
        ]);
	}

    public function uploadUrl($attachmentMark, $table, $field, $id)
    {
        return Yii::getAlias('@backendurl') . "/upload/{$attachmentMark}/{$table}/{$field}.html?mparam=&id={$id}";
    }

    public function getModelMenuCode($method)
    {
        $data = $this->getPointModel('menu')->getInfo(['where' => ['elem_code' => $this->modelCode, 'method' => $method]]);
        return empty($data) ? '' : $data['code'];
    }
}
