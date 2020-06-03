<?php
namespace common\ueditor;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\InputWidget;

class UEditor extends InputWidget
{
    //配置选项，参阅Ueditor官网文档(定制菜单等)
    public $clientOptions = [];

    //默认配置
    protected $_options;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
		$model = $this->model;
        $this->id = $this->hasModel() ? Html::getInputId($model, $this->attribute) : $this->id;
        $this->_options = [
            //'serverUrl' => '/upload.html?model=',// . $this->model->code,//Url::to(['upload']),
			'serverUrl' => "/upload/{$model->attachmentMark}/upeditor.html?table={$model->shortTable}&field={$this->attribute}&id={$model->id}",
            'initialFrameWidth' => '100%',
            'initialFrameHeight' => '400',
			'pictureId' => Html::getInputId($model, 'picture_' . $this->attribute),
			'descriptionId' => Html::getInputId($model, 'description'),
            'lang' => (strtolower(Yii::$app->language) == 'en-us') ? 'en' : 'zh-cn',
        ];
        $this->clientOptions = ArrayHelper::merge($this->_options, $this->clientOptions);
        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id]);
        } else {
            return Html::textarea($this->id, $this->value, ['id' => $this->id]);
        }
    }

    /**
     * 注册客户端脚本
     */
    protected function registerClientScript()
    {
        UEditorAsset::register($this->view);
        $clientOptions = Json::encode($this->clientOptions);
        $script = "var ueditor = UE.getEditor('" . $this->id . "', " . $clientOptions . ");";
		$script .= "ueditor.addListener('blur',function(){
    		var imgs = UE.dom.domUtils.getElementsByTagName(ueditor.document, 'img');
    
			var pIds = '';
            for (var i = 0, ci; ci = imgs[i++];) {
                var src = ci.getAttribute('_src') || ci.src || '';
    			var query = src.split('?')[1];
    			if (!query) {
    				continue;
    	        }
    			query = query.split('&')[0];
    			pIds += query.replace('aid=', '') + ',';
            }
	        $('#' + '{$this->clientOptions['pictureId']}').val(pIds);
            //var editor=UE.getEditor('content');
            //var arr =(UE.getEditor('content').getContentTxt());
            //var description = document.getElementById('description');//摘要id
            //description.value=arr.substring(0,180);
        });";
    
        $this->view->registerJs($script, View::POS_READY);
    }
}
