<?php
return [
    'modules' => [
        'merchant' => [
            'class' => 'backend\merchant\Module',
        ],
        'infocms' => [
            'class' => 'backend\infocms\Module',
        ],
        'shop' => [
            'class' => 'backend\shop\Module',
        ],
        'foundation' => [
            'class' => 'backend\foundation\Module',
        ],
        'rest-client' => [
            'class' => 'backend\resttool\Module',
            'baseUrl' => Yii::getAlias('@restappurl'),
        ],
    ],
];
