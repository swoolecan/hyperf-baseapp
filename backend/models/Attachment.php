<?php

namespace backend\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * This is the base model class for attachment.
 */
class Attachment extends BaseModel
{
    //const DEFAULT_URL_PREFIX = 'default';
    //const DEFAULT_PATH_PREFIX = 'default';

    /**
     * @var UploadedFile
     */
	static public $_mark;
    public $files;
    public $file;
    public $noFile;
    public $filePre = '';
    public $urlPrefix = '';
	public $currentMark;
	public $_currentDb;

    /**
     * @var string Upload path
     */
    public $uploadPath = '@uploadPath';

    /**
     * @var integer the level of sub-directories to store uploaded files. Defaults to 1.
     * If the system has huge number of uploaded files (e.g. one million), you may use a bigger value
     * (usually no bigger than 3). Using sub-directories is mainly to ensure the file system
     * is not over burdened with a single directory having too many files.
     */
    public $directoryLevel = 1;

	public function initMark($mark)
	{
		$markInfos = $this->appAttr->getEnvironmentParams('attribute', 'attachment-mark');
		$markInfo = $markInfos[$mark];
		self::$_mark = $markInfo['db'];
		$model = new self();
		$model->filePre = isset($markInfo['filePre']) ? $markInfo['filePre'] : ($mark == 'db' ? 'default' : $mark);
		$model->urlPrefix = isset($markInfo['urlPrefix']) ? $markInfo['urlPrefix'] : ($mark == 'db' ? 'default' : $mark);
		return $model;
	}

    public static function getDb()
    {
		$db = self::$_mark;
		$db = empty($db) ? 'db' : $db;
        return Yii::$app->$db;//$currentDb;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info_field'], 'filterTableField'],
            ['orderlist', 'default', 'value' => 0],
            [['size', 'info_id', 'orderlist'], 'integer'],
            [['info_table', 'info_field', 'info_id'], 'default', 'value' => function($model, $attribute) {
                return $model->$attribute;
            }],
            /*[['url_prefix'], 'default', 'value' => function($model, $attribute) {
                return !empty($model->url_prefix) ? $model->url_prefix : self::DEFAULT_URL_PREFIX;
            }],
            [['path_prefix'], 'default', 'value' => function($model, $attribute) {
                return !empty($model->path_prefix) ? $model->path_prefix : self::DEFAULT_PATH_PREFIX;
            }],*/

            [['file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false],

            [['name'], 'string', 'max' => 64],
            [['name', 'size'], 'default', 'value' => function($model, $attribute) {
                return $model->file->$attribute;
            }],
            [['size'], 'filterSize'],

            [['type'], 'string', 'max' => 32],
            [['type'], 'default', 'value' => function() {
				$type = FileHelper::getMimeType($this->file->name);
				$type = empty($type) ? strval($this->file->type) : $type;
				return $type;
                //$type = isset($this->file->type) && !empty($this->file->type) ? $this->file->type : FileHelper::getMimeType($this->file->tempName);
                //return $type == null ? 'image/jpeg' : $type;
            }],
            [['type'], 'filterType'],
            ['extname', 'default', 'value' => function() {
				$info = pathinfo($this->file->name);
				$ext = $info['extension']; 
				if (empty($ext)) {
                    $ext = FileHelper::getExtensionsByMimeType($this->type);
                    $ext = is_array($ext) ? array_pop($ext) : '';
				}
                return empty($ext) ? 'jpg' : $ext;
            }],

            [['filepath'], 'string', 'max' => 256],
            [['filepath'], 'default', 'value' => function() {
                $key = md5($this->file->name . rand(0, 1000));
                $base = $this->getPathPre();
                if ($this->directoryLevel > 0) {
                    for ($i = 0; $i < $this->directoryLevel; ++$i) {
                        if (($prefix = substr($key, $i + $i, 2)) !== false) {
                            $base .= "/{$prefix}";
                        }
                    }
                }
                return $base . "/{$key}.{$this->extname}";
            }],
            [['description', 'tag'], 'safe'],
        ];
    }

    public function filterTableField()
    {
        $condition = $this->getFieldInfos($this->info_table, $this->info_field);
        if ($condition === false) {
            $this->addError('info_table', '字段信息有误');
        }
        $this->created_at = time();

        return true;
    }

    public function filterSize()
    {
        $condition = $this->getFieldInfos($this->info_table, $this->info_field);
        $minSize = isset($condition['minSize']) ? $condition['minSize'] : 0.1;
        $maxSize = isset($condition['maxSize']) ? $condition['maxSize'] : 20;
        if ($this->size < $minSize * 1024 || $this->size > $maxSize * 1024) {
            $this->addError('size', "文件大小不能小于{$minSize}Kb，大于{$maxSize}Kb");
        }

        return true;
    }

    public function filterType()
    {
        $condition = $this->getFieldInfos($this->info_table, $this->info_field);
        $types = (array) $condition['type'];
        $typeStr = '';
        foreach ($types as $type) {
            if ($this->type == $type || $type == '*') {
                return true;
            }
            $typePrefix = substr($type, 0, strpos($type, '*'));
            if (!empty($typePrefix) && strpos($this->type, $typePrefix) !== false) {
                return true;
            }
            $typeStr .= $type . ';';
        }
        $this->addError('type', "文件类型只能是{$typeStr}");
        return false;
    }

    /**
     * @inherited
     */
    public function beforeSave($insert)
    {
        if ($this->noFile) {
            return true;
        }
        if (!isset($this->file) || !($this->file instanceof UploadedFile)) {
            return false;
        }
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $filepath = $this->getPath();
        FileHelper::createDirectory(dirname($filepath));
        return $this->file->saveAs($filepath, false);
    }

	public function getPathPre()
	{
		//return "{$this->filePre}/{$this->info_table}/{$this->info_field}";
		return "{$this->info_table}/{$this->info_field}";
	}

	public function getPath($withFile = true)
	{
        $pathParams = Yii::$app->params['pathParams'];
        $path = isset($pathParams[$this->filePre]) ? $pathParams[$this->filePre] : '';
		$path = rtrim($path, '/') . '/';
		if (empty($withFile)) {
			return $path;
		}
		return $path . $this->filepath;
	}

	public function getUrlBase()
	{
        $urlPrefix = empty($this->urlPrefix) ? 'default' : $this->urlPrefix;
        $urlParams = Yii::$app->params['urlParams'];
        $urlBase = isset($urlParams[$urlPrefix]) ? $urlParams[$urlPrefix] : '';

        return trim($urlBase, '/') . '/';
	}

    public function getUrl($filepath = null)
    {
        $filepath = is_null($filepath) ? $this->filepath : $filepath;
        return $this->getUrlBase() . $filepath;
    }

    /**
     * @inherited
     */
    public function beforeDelete()
    {
        $filePath = $this->getPath();
        if (file_exists($filePath)) {
            //unlink($filePath);
        }
        return true;
    }

    public static function updateBindInfo($condition, $ids, $infoId)
    {
        $ids = (array) $ids;
        $infos = self::find()->where($condition)->all();
        foreach ($infos as $info) {
            if (!in_array($info->id, $ids)) {
                $info->delete();
                continue ;
            } else {
                $info->info_id = $infoId;
                $info->in_use = 1;
                $info->noFile = true;
                $info->update(false);
            }
        }

        return ;
    }

    public function getFieldIds($table, $field, $infoId)
    {
        if (empty($table) || empty($field) || empty($infoId)) {
            return '';
        }

        $condition = [
            'info_table' => $table,
            'info_field' => $field,
            'info_id' => $infoId,
            'in_use' => 1,
        ];
        $infos = $this->find()->indexBy('id')->where($condition)->all();

        if (empty($infos)) {
            return '';
        }
        return implode(',', array_keys($infos));
    }

    public function getFieldInfos($table = null, $field = null)
    {
		$fieldDefault = [
        	'isSingle' => true,
    		'minSize' => 1, // unit: kb
    		'maxSize' => 1000,
    		'type' => 'image/*',
		];
        $infos = $this->appAttr->getEnvironmentParams('attribute', 'attachment-field');

        if (is_null($table) && is_null($field)) {
            return $infos;
        }

        if (!isset($infos[$table])) {
            return false;
        }
        if (is_null($field)) {
            return $infos[$table];
        }
        if (!isset($infos[$table][$field])) {
            return false;
        }

        return array_merge($fieldDefault, $infos[$table][$field]);
    }

    /**
     * 更新附件信息
     */
    public function updateInfo($id, $infoId, $extData)
    {
        $info = $this->findOne($id);
        if (empty($info)) {
            return ;
        }
        $info->info_id = $infoId;
           $info->in_use = 1;
           $info->noFile = true;

        // 部分数据表会有一些专有的字段
        if (!empty($extData)) {
            foreach ($extData as $field => $value) {
                $info->$field = $value;
            }
        }
        // 处理附件的常用属性，名称、排序和描述
        $attrs = ['filename', 'is_master', 'orderlist', 'description'];
        $requestObj = Yii::$app->request;
        if (Yii::$app->id != 'app-console') {
        foreach ($attrs as $attr) {
            $params = Yii::$app->request->post('attachment_' . $attr, '');
            $value = isset($params[$id]) ? $params[$id] : '';
            $value = $attr == 'orderlist' ? intval($value) : $value;
            $value = $attr == 'is_master' ? intval($value) : $value;
            $info->$attr = $value;
        }
        }
        $r = $info->update(false);
        return $info['is_master'];
    }

    /**
     * 删除没用的附件
     */
    public function deleteInfo($where, $noDeleteIds)
    {
        $infos = $this->find()->where($where)->all();
        foreach ($infos as $info) {
            if (in_array($info->id, (array) $noDeleteIds)) {
                continue;
            }
            $info->noFile = true;
			$info->in_use = 0;
			$info->update(false, ['in_use']);
            //$info->delete();
        }
        return ;
    }

    public function getFileSize()
    {
        $size = getimagesize($this->path);
        return ['width' => $size[0], 'height' => $size[1]];
    }

	public function _privMerchantId()
	{
		return true;
	}	

	public function getInfoBySource($value, $field = 'source_url')
	{
		$info = $this->getInfo($value, $field);
		exit();
	}
}
