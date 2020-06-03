<?php

namespace common\components;

use Yii;
use yii\helpers\Html;
use yii\web\View as ViewBase;
use common\ueditor\UEditor;
use views\components\WpshopTrait;
use views\components\WshopTrait;

class View extends ViewBase
{
    use ViewTrait;
    //use ViewDomTrait;

    public function beforeRender($viewFile, $params)
    {
		$result = parent::beforeRender($viewFile, $params);
		$controller = $this->context;
		//print_R($controller);exit();
		if (method_exists($controller, 'initGlobalDatas')) {
		    $globalElems = $controller->initGlobalDatas();
		}
        return $result;
    }

    public function getCurrentTitle()
    {
        if (!empty($this->title)) {
            return $this->title;
        }
        $title = $this->contextDatas('menuInfos', 'menuTitle');
        $title = empty($title) ? $this->getSiteElem('name') : $title;
        $this->title = $title;
        return $title;
    }

	public function getPointDatas($code, $params = [], $forceNew = false)
	{
		return $this->context->getPointDatas($code, $params, $forceNew);
	}

    public function getPointUrl($code, $params = '', $client = null)
    {
        return $this->context->getPointUrl($code, $params, $client);
    }

	public function getPassportMenuUrl($code)
	{
		return $this->context->getPassportMenuUrl($code);
	}

    public function getPagesysElem($elem)
    {
        return $this->context->getPagesysElem($elem);
    }

    public function getSiteElem($elem)
    {
        return $this->context->getSiteElem($elem);
    }

    public function getMenuData($code)
    {
        $menus = $this->contextDatas('menuInfos', 'menus');
        return isset($menus[$code]) ? $menus[$code] : [];
    }

    public function getMenuApp($method)
    {
        $menus = $this->contextDatas('menuInfos', 'appMenus');
        return isset($menus[$method]) ? $menus[$method] : [];
    }

    public function getPointParam($code, $default = '')
    {
        return isset($this->params[$code]) ? $this->params[$code] : $default;
    }

    public function initBackendMenus()
    {
        $menuInfos = $this->params['menuInfos'] = $this->context->menuInfos;
        $this->params['menus'] = $menuInfos['menus'];
        $this->params['appMenus'] = $menuInfos['appMenus'];
        $this->params['currentMenu'] = $menuInfos['currentMenu'];
        $this->params['parentMenu'] = $menuInfos['parentMenu'];
    }

    public function appDatas($code, $indexName = null)
    {
        return $this->context->appContextDatas($code, 'app', $indexName);
    }

    public function contextDatas($code, $indexName = null)
    {
        return $this->context->appContextDatas($code, 'context', $indexName);
    }

    public function getElemView($model, $field, $elem, $isNew = false)
    {
        $isNew = $model->isNewRecord ? true : false;
        $sort = isset($elem['sort']) ? $elem['sort'] : 'show';
        $value = $this->_getViewValue($model, $field, $elem);
        if ($sort == 'show') {
		    if (isset($elem['elemId'])) {
			    $value = "<span id='{$elem['elemId']}'>{$value}</span>";
		    }
            return "<td>{$value}</td>";
        }
		if ($sort == 'change' && isset($elem['onlyEmpty']) && !empty($value)) {
            return "{$value}";
		}
        $type = isset($elem['type']) ? $elem['type'] : 'common';
        $method = "_{$type}View";
        $elemValue = $this->$method($value, $model, $field, $elem, $isNew);
        if (isset($elem['noWrap'])) {
            return $elemValue;
        }
        return "<td>{$elemValue}</td>";
    }

    protected function _getElemUrl($elem, $model = null)
    {
        $menuCode = isset($elem['menuCode']) ? $elem['menuCode'] : $model->getModelMenuCode('update');
        $url = isset($menuCode) ? $this->getMenuUrl($menuCode) : $this->context->clientUrl;
		$url = $this->context->appendUrlParam($url, ['update_batch' => 1]);
		return $url;
    }

    public function getMenuUrl($menuCode, $params = [])
    {
        $menu = $this->getMenuData($menuCode);
        $menu = empty($menu) ? $this->getMenuApp($menuCode) : $menu;
		if (!isset($menu['url'])) {
			return '';
		}
        $url = $menu['url'];
		if (empty($params)) {
			return $url;
		}
		$queryStr = http_build_query($params);
		$queryStr = strpos($url, '?') !== false ? "&{$queryStr}" : "?{$queryStr}";
		return $url . $queryStr;
    }

    protected function _getViewValue($model, $field, $elem)
    {
        if (isset($elem['value'])) {
            return $elem['value'];
        }

        $valueType = isset($elem['valueType']) ? $elem['valueType'] : 'common';
        switch ($valueType) {
        case 'key':
            $value = $model->getKeyName($field, $model->$field);
            break;
        case 'point':
            $pField = empty($elem['pointField']) ? 'id' : $elem['pointField'];
            $where = [$pField => $model->$field];
			$nameField = isset($elem['nameField']) ? $elem['nameField'] : 'name';
            $value = $model->getPointName($elem['table'], $where, $nameField);
            break;
        case 'timestamp':
            $format = isset($elemValue['format']) ? $elemValue['format'] : null;
            $value = $model->formatTimestamp($model->$field, $format);
            break;
        case 'inline':
            $method = $elem['method'];
            $value = $model->$method();
            break;
        default:
            $value = $model->$field;
        }
        return $value;
    }

    protected function _timestampView($value, $model, $field, $elem, $isNew)
    {
        $id = (int) $model->id;
        $fName = $model->formName();
        $idClass = "{$fName}_{$field}_{$id}";
        $url = $this->_getElemUrl($elem, $model);
        $onblur = $isNew ? '' : "changeDate(\"{$url}\", {$id}, \"{$field}\", this.value);";
        $format = isset($elem['format']) ? $elem['format'] : 'Y-m-d H:i:s';
        $formatFront = isset($elem['formatFront']) ? $elem['formatFront'] : 'YYYY-MM-DD HH:mm:ss';
        $value = !empty($value) ? $value : date($format);
        $str = "<input type='hidden' id='{$idClass}_old' value='{$value}' />";
        $str .= "<input class='form-control' type='text' id='{$idClass}' onblur='{$onblur}' value='{$value}' />";
        $str .= "<script type='text/javascript'>
                    $(function () {
                        $('#{$idClass}').datetimepicker({locale: 'zh-CN', format: '{$formatFront}'});
                    });
                </script>";
        return $str;
    }

    protected function _dropdownMulView($value, $model, $field, $elem, $isNew)
    {
        $id = (int) $model->id;
        $fName = $model->formName();
        $idClass = "{$fName}_{$field}_{$id}";
		$url = $this->_getElemUrl($elem, $model);
        $onchange = isset($elem['onchange']) ? $elem['onchange'] : ($isNew ? '' : "updateElemByBatch(\"{$url}\", {$id}, \"{$field}\", dropdownMulValues(\"{$idClass}\"));");

        $option = isset($elem['option']) ? $elem['option'] : [];
        $option = array_merge($option, [
            'prompt' => '全部',
            'onchange' => $onchange,
            'id' => $idClass,
            'multiple' => 'multiple',
            'class' => 'form-control',
        ]);
        return Html::dropDownList($field, $value, $elem['elemInfos'], $option);
    }

    protected function _dropdownView($value, $model, $field, $elem, $isNew)
    {
        $id = (int) $model->id;
        $fName = $model->formName();
        $idClass = "{$fName}_{$field}_{$id}";
		$url = $this->_getElemUrl($elem, $model);
        $onchange = isset($elem['onchange']) ? $elem['onchange'] : ($isNew ? '' : "updateElemByBatch(\"{$url}\", {$id}, \"{$field}\", this.value);");
        if (isset($elem['cascade'])) {
			$cascade = $elem['cascade'];
            $onchange .= $this->_formatCascade($cascade, $model, $fName, $elem['cascade']);
			$elem['elemInfos'] = isset($elem['elemInfos']) ? $elem['elemInfos'] : $model->getCascadeInfos($field);
        }

        $option = isset($elem['option']) ? $elem['option'] : [];
        $option = array_merge($option, [
            'prompt' => '全部',
            'onchange' => $onchange,
            'id' => $idClass,
            'class' => 'form-control',
        ]);
        return Html::dropDownList($field, $value, $elem['elemInfos'], $option);
    }

    protected function _formatCascade($cascade, $model, $fName, $elem)
    {
		if (empty($cascade['childElem'])) {
			return '';
		}
		$id = (int) $model->id;
        $urlBase = isset($cascade['url']) ? $cascade['url'] : $this->getMenuUrl($cascade['mCode']);
		$uParams = [
			'point_format_list' => 'cascade',
			'field_key' => isset($elem['field_key']) ? $elem['field_key'] : 'id',
			'field_value' => isset($elem['field_value']) ? $elem['field_value'] : 'name',
		];
        $urlBase = $this->context->appendUrlParam($urlBase, $uParams);
        
        $casUrl = $urlBase . '&' . $cascade['parentParam'] . '=';//" + ' . '$(this).val()';
        $childElem = $cascade['childElem'];
        $childId = "{$fName}_{$childElem}_{$id}";
        $cascadeStr = '$.get("' . $casUrl . '" + $(this).val(), function(data) {'
			. 'var htmlContent;'
            //. 'var htmlContent = "<option value=>请选择' . $model->getAttributeLabel($childElem) . '</option>";'
            . '$.each(data.datas, function(i, v) {'
            . 'var checkStr = i == "' . $model->$childElem . '" ? "selected" : "";'
            . 'htmlContent += "<option value=\"" + i + "\" + checkStr + >" + v + "</option>";'
            . ' }); $("#' . $childId . '").html(htmlContent); $("#' . $childId . '").trigger("onchange"); });';
		return $cascadeStr;
	}

    protected function _textareaView($value, $model, $field, $elem, $isNew)
    {
        $id = (int) $model->id;
        $fName = $model->formName();
        $idClass = "{$fName}_{$field}_{$id}";
        $url = $this->_getElemUrl($elem, $model);
        $onchange = $isNew ? '' : "updateElemByBatch(\"{$url}\", {$id}, \"{$field}\", this.value);";
        $option = isset($elem['option']) ? $elem['option'] : [];
        $option = array_merge([
            'id' => $idClass,
            'rows' => 3,
            'cols' => '60',
            'onchange' => $onchange,
        ], $option);
        return Html::textarea($field, $value, $option);
    }

    protected function _editorView($value, $model, $field, $elem, $isNew)
    {
        $id = (int) $model->id;
        $fName = $model->formName();
        $idClass = "{$fName}_{$field}_{$id}";
        $url = $this->_getElemUrl($elem, $model);
        $onchange = $isNew ? '' : "updateElemByBatch(\"{$url}\", {$id}, \"{$field}\", this.value);";
        $option = isset($elem['option']) ? $elem['option'] : [];
        $option = array_merge([
            'id' => $idClass,
            'rows' => 3,
            'cols' => '100',
            'onchange' => $onchange,
        ], $option);
        return Html::textarea($field, $value, $option);
    }

    protected function _commonView($value, $model, $field, $elem, $isNew)
    {
        $id = (int) $model->id;
        $fName = $model->formName();
        $idClass = "{$fName}_{$field}_{$id}";
        $url = $this->_getElemUrl($elem, $model);
        $onchange = $isNew ? '' : "updateElemByBatch(\"{$url}\", {$id}, \"{$field}\", this.value);";
        $width = isset($elem['width']) ? "style='width: {$elem['width']}px' " : '';
        return "<input type='text' id='{$idClass}' name='{$field}' {$width}value='{$value}' onchange='{$onchange}' />";
    }

    public function getOptionInfo($elem, $optionDefault)
    {
        $optionDefault = array_merge([
            'id' => $elem['field'] . '_field',
        ], $optionDefault);
        $option = isset($elem['option']) ? array_merge($optionDefault, $elem['option']) : $optionDefault;
        if (!isset($elem['ajax'])) {
            return $option;
        }

        $data = $elem['ajax'];
        $menu = $this->getMenuData($data['menuCode']);
        if (empty($menu)) {
            return $option;
        }
        $url = $menu['url'];
        $option['onchange'] = '$.get("' . $menu['url'] . '?action=infoAjax&' . $elem['field'] . '="+$(this).val(),function(data){
            var htmlContent = "";
            $.each(data, function(i, v) {
                htmlContent += "<option value=\"" + i + "\">" + v + "</option>";
            });
            $("#' . $data['targetField'] . '_wrap").removeClass("hidden");

            $("#' . $data['targetField'] . '_field").html(htmlContent);
        });';
        return $option;
    }
}
