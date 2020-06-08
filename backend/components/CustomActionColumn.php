<?php

namespace backend\components;

use Yii;
use yii\helpers\Html;
use yii\grid\ActionColumn;

class CustomActionColumn extends ActionColumn
{
    public $template = '{view} {update} {delete}';

    public function init()
    {
        parent::init();
        $this->buttonOptions = array_merge([
            'view' => [],
            'update' => [],
            'delete' => [],
            'authority' => [],
            'other' => [],
        ], (array) $this->buttonOptions);
        $this->initDefaultButtons();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
	{
		$elems = [
			'view' => ['icon' => 'eye-open'], 
			'update' => ['icon' => 'pencil'], 
			'delete' => ['icon' => 'trash'], 
			'authority' => ['icon' => ''],
		];
		foreach ($elems as $elem => $eData) {
			$baseOptions = [
                'title' => Yii::t('yii', ucfirst($elem)),
                'aria-label' => Yii::t('yii', ucfirst($elem)),
                'data-pjax' => '0',
            ];
			if ($elem == 'delete') {
				$baseOptions = array_merge($baseOptions, [
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
				]);
			}

            if (!isset($this->buttons[$elem])) {
                $this->buttons[$elem] = function ($url, $model, $key) use ($elem, $eData, $baseOptions) {
                    $options = array_merge($baseOptions, $this->buttonOptions[$elem]);
    				$check = method_exists($model, 'checkBackendPriv') ? $model->checkBackendPriv($options['mCode']) : true;
					$title = empty($eData['icon']) ? Yii::t('admin-common', ucfirst($elem)) : '<span class="glyphicon glyphicon-' . $eData['icon'] . '"></span>';
                    return $check ? Html::a($title, $url, $options) : '';
                };
            }
		}
    }
}
