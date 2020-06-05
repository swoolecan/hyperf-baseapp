<?php

namespace baseapp\models;

use Yii;

trait ArticleTrait
{
    public $thumb;
	public $picture_content;
	public $tags;

	public function attributeExt()
	{
		return [
			'sort_parent' => '攻略一级分类',
		];
	}

	public function _afterSaveOpe($insert, $changedAttributes)
	{
		return true;
	}	

    public function _getSingleAttachments()
    {
		return ['thumb'];
    }

	public function getFillTagInfo()
	{
		return true;
	}

	protected function getFillDescription()
	{
		return 'content';
	}

    public function getThumbUrl()
	{
		return $this->_getThumbExt('thumb', 'picture_content');
	}

    public function formatImgtag($field = null, $info = [])
    {
        $url = $this->getAttachmentImgtag('thumb');
		if (empty($url)) {
            $url = $this->getAttachmentImgtag('picture_content');
		}
		return $url;
    }
}
