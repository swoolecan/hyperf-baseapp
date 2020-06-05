<?php
return [
    'log',
    function () {
        if (!isset(Yii::$app->i18n->translations['rbac-admin'])) {
            Yii::$app->i18n->translations['rbac-admin'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'zh-CN',
                'basePath' => '@common/messages'
            ];
            Yii::$app->i18n->translations['admin-common'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@common/messages'
            ];
            Yii::$app->i18n->translations['common'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@common/messages'
            ];
        }
    }
];
