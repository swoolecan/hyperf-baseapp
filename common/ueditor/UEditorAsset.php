<?php
namespace common\ueditor;


use yii\web\AssetBundle;

class UEditorAsset extends AssetBundle
{
    public $baseUrl = '@backendurl/assets';
    public $basePath = '@backend/assets';

    public $js = [
        'ueditor.config.js',
        'ueditor.all.js',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}
