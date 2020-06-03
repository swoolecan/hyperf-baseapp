<?php

namespace common\models\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\FileHelper;

trait Attachment
{
    /**
     * 附件类型的字段信息更新时，是否删除旧的附件，默认删除
     */
    public $deleteAttachment = true;

	public function getAttachmentModel()
	{
		$modelMid = $this->getPointModel('attachment');
		$model = $modelMid->initMark($this->attachmentMark);
		return $model;
	}

	public function getAttachmentMark()
	{
		return 'base';
	}

    public function getAttachmentUrl($id)
    {
        $info = $this->getAttachmentInfo($id);
        return empty($info) ? '' : $info->getUrl();
    }

    public function getAttachmentPath($id)
    {
        $info = $this->getAttachmentInfo($id);
        return empty($info) ? '' : $info->getPath();
    }

    protected function getFieldModel($field)
    {
        $info = $this->getAttachmentInfo($this->attachmentWhere($field));
		$info = !empty($info) ? $info : $this->getAttachmentInfo($this->attachmentWhere($field, false));
        return $info;
    }

    public function getPictureUrl()
    {
        return $this->_getThumb('picture');
    }

    public function getPhotoUrl()
    {
        return $this->_getThumb('photo');
    }

    public function getLogoUrl()
    {
        return $this->_getThumb('logo');
    }

	public function getThumbUrl()
	{
		return $this->_getThumb('thumb');
	}

    public function _getThumb($field)
    {
        $thumbUrl = $this->getAttachmentUrl($this->attachmentWhere($field));
		$thumbUrl = !empty($thumbUrl) ? $thumbUrl : $this->getAttachmentUrl($this->attachmentWhere($field, false));
		return $thumbUrl;
    }

    public function _getThumbExt($field, $ext)
	{
        $url = $this->_getThumb($field);
        $url = empty($url) ? $this->_getThumb($ext) : $url;
        return $url;
	}

    public function getAttachmentImg($id, $pointSize = true, $options = [])
    {
        $url = $this->getAttachmentUrl($id);
        if ($url) {
            $optionsDefault = [
                'style' => ['width' => '100px', 'height' => '80px'],
                'onclick' => 'window.open(this.src);',
            ];
            $options = $pointSize && empty($options) ? $optionsDefault : $options;
            return Yii::$app->formatter->asImage($url, $options);
        }
        return '';
    }

    public function getAttachmentImgtag($field, $forceMain = false, $pointSize = true, $options = null)
    {
        $info = $this->getAttachmentInfo($this->attachmentWhere($field));
        $info = !empty($info) ? $info : ($forceMain ? $info : $this->getAttachmentInfo($this->attachmentWhere($field, false)));
        if ($info) {
            $url = $info->getUrl();
			return $this->formatImage($url, $options);
        }
        return '';
    }

    protected function _updateSingleAttachment($fields, $extData = [])
    {
        $attachment = $this->attachmentModel;
        foreach ($fields as $field) {
            if (is_null($this->$field)) {
                continue;
            }
            $aIds = array_filter(explode(',', $this->$field));
            $aId = array_pop($aIds);
            $attachment->updateInfo($aId, $this->id, $extData);

            $where = ['info_table' => $this->shortTable, 'info_field' => $field, 'info_id' => $this->id];
            $this->deleteAttachment && $attachment->deleteInfo($where, $aId);
        }

        return ;
    }

    protected function _updateMulAttachment($field, $extData = [])
    {
        if (is_null($this->$field)) {
            return '';
        }
        $attachment = $this->attachmentModel;
        $ids = array_filter(explode(',', $this->$field));
        foreach ($ids as $id) {
            $attachment->updateInfo($id, $this->id, $extData);
        }

        $where = ['info_table' => $this->shortTable, 'info_field' => $field, 'info_id' => $this->id];
        $this->deleteAttachment && $attachment->deleteInfo($where, $ids);

        return ;
    }

    public function _importDatas($returnAttachment = false)
    {   
        $aId = $this->import;
        if (empty($aId)) {
            $this->addError('import', '参数错误');
            return false;
        }   

        $attachment = $this->getAttachmentInfo($aId);
        if (empty($attachment)) {
            $this->addError('import', '指定的文件参数有误，请重新上传');
            return false;
        }   
        $file = $attachment->getPath();
        if (!file_exists($file)) {
            $this->addError('import', '指定的文件不存在，请重新上传');
            return false;
        }   
        $datas = (array) $this->importDatas($file);
        if (empty(array_filter($datas))) {
            $this->addError('import', '没有数据');
            return false;
        }
        if ($returnAttachment) {
            $datas = [
                'attachment' => $attachment,
                'datas' => $datas,
            ];
        }
        return $datas;
    }

    public function getAttachmentIds($field)
    {
        return $this->attachmentModel->getFieldIds($this->shortTable, $field, $this->id);
    }

    public function getAttachmentInfo($where)
    {
		$params = [
			'where' => is_array($where) ? $where : ['id' => $where],
			'orderBy' => ['orderlist' => SORT_DESC],
		];
        $info = $this->attachmentModel->getInfo($params);
        if (!empty($info)) {
            $info->urlPrefix = $this->attachmentModel->urlPrefix;
            $info->filePre = $this->attachmentModel->filePre;
        }
        return $info;
    }

    public function getAttachmentInfos($field, $asArray = true)
    {
        $model = $this->attachmentModel;
		$params = [
			'where' => $this->attachmentWhere($field, false),
			'orderBy' => ['orderlist' => SORT_DESC],
		];
        $aInfos = $model->getInfos($params);
        foreach ($aInfos as $aInfo) {
            $aInfo->urlPrefix = $model->urlPrefix;
            $aInfo->filePre = $model->filePre;
        }
		if (empty($asArray)) {
			return $aInfos;
		}

        $aDatas = [];
        foreach ($aInfos as $attachment) {
            $url = $attachment->getUrl();
            $aDatas[] = [ 
                'url' => $attachment->getUrl(),
                'name' => $attachment['filename'],
                'description' => $attachment['description'],
            ];  
        }    
		return $aDatas;
    }

    public function attachmentWhere($field, $isMaster = true)
    {
        $condition = [ 
            'info_table' => $this->shortTable,
            'info_field' => $field,
            'info_id' => $this->id,
            'in_use' => 1,
        ];  
        if ($isMaster) {
            $condition['is_master'] = 1;
        }
        return $condition;
    }

	public function getFileExt($file)
	{
        $pathInfo = pathinfo($file);
        $extName = isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
        $extName = $pos = strpos($extName, '?') ? substr($extName, 0, strpos($extName, '?')) : $extName;
		return $extName;
	}

	public function getFileType($file)
	{
		$type = FileHelper::getMimeType($file);
		$type = strval($type);
		return $type;
	}
}
