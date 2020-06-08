<?php

namespace backend\models;

class Imexport extends BaseModel
{
    public $import;
    public $export;
    public $description;
	public $prefix;
	public $method;

    public static function tableName()
    {
        return '{{%attachment}}';
    }

    public function rules()
    {
        return [
            [['import', 'export', 'description', 'prefix', 'method'], 'safe'],
        ];
    }

    public function import()
    {   
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        if (empty($this->prefix)) {
            exit('请选择渠道');
        }
		$modelClass = "{$this->prefix}\Imexport";
		$model = new $modelClass;
        $datas = $this->_importDatas();
		$method = !empty($this->method) ? "{$this->method}Datas" : 'dealDatas';
		return $model->$method($datas[0]);
	}
}
