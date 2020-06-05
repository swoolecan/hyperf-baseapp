<?php

namespace common\components;

use Yii;
use yii\helpers\Html;
use yii\web\View as ViewBase;
use common\ueditor\UEditor;

trait ViewTrait
{
    public function _inputCreateElem($data)
    {
        return "<input name='{$data['nameStr']}' value='{$data['value']}' />";
    }

    public function _daytimeCreateElem($data)
    {
        return $this->_timestampCreateElem($data);
    }

    public function _timestampCreateElem($data)
    {
        $str = '';
        $sort = isset($data['sort']) ? $data['sort'] : 'timestamp';
        $value = isset($data['value']) ? $data['value'] : null;
        $value = is_null($value) ? date('Y-m-d H:i:s') : ($sort == 'timestamp' ? date('Y-m-d H:i:s', intval($value)) : $value);
        $formats = [
            'timestamp' => 'YYYY-MM-DD HH:mm:ss',
            'daytime' => 'YYYY-MM-DD HH:mm:ss',
            'day' => 'YYYY-MM-DD',
        ];
        $idStr = $data['idStr'];
        $nameStr = $data['nameStr'];
        $format = $formats[$sort];
        $str .= "<input class='form-control' type='text' id='{$idStr}' name='{$nameStr}' value='{$value}'>"
             . "<script type='text/javascript'>"
             . "$(function () { $('#{$idStr}').datetimepicker({locale: 'zh-CN', format: '{$format}'});});</script>";
        return $str;
    }

    public function _radioCreateElem($data)
    {
		$haveNew = isset($data['new']) && !empty($data['new']) ? $data['new'] : false;
        $value = isset($data['value']) ? $data['value'] : '';
		$option = isset($data['option']) ? $data['option'] : [];
		$option = array_merge(['inline' => true], $option);
        $str = Html::radioList(
            "{$data['nameStr']}",
            $value,
            $data['valueInfos'],
			$option
        );
		$newStr = $haveNew ? $this->_inputNewCreateElem($data['new']) : '';
        $str = str_replace('</div>', ' ' . $newStr . '</div>', $str);
		return $str;
	}

    public function _checkboxCreateElem($data)
    {
		$haveNew = isset($data['new']) && !empty($data['new']) ? $data['new'] : false;
        $value = isset($data['value']) ? $data['value'] : '';
		$value = is_string($value) && strpos($value, ',') !== false ? explode(',', $value) : $value;

		$option = isset($data['option']) ? $data['option'] : [];
		$option = array_merge(['inline' => true], $option);
        $str = Html::checkboxList(
            "{$data['nameStr']}[]",
            $value,
            $data['valueInfos'],
			$option
        );
		$newStr = $haveNew ? $this->_inputNewCreateElem($data['new']) : '';
        $str = str_replace('</div>', ' ' . $newStr . '</div>', $str);
		return $str;
    }

	public function _inputNewCreateElem($data)
	{
        $str = "<input id='{$data['idStr']}' name='{$data['nameStr']}' value='' />  ";
		$option = isset($data['option']) ? $data['option'] : [];
		$option = array_merge($option, ['class' => 'btn btn-success']);

        $str .= Html::submitButton('添加', $option);
		return $str;
	}

	public function highlightKeyword($keyword, $string)
	{
		if (empty($keyword) || empty($string)) {
			return $string;
		}
		if (strpos($string, $keyword) !== false) {
			$string = str_replace($keyword, '<b style="color:red;">' . $keyword . '</b>', $string);
		}
		return $string;
	}
}
