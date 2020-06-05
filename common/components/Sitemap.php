<?php

namespace common\components;

use yii\base\Component;

class Sitemap extends Component
{
    public $siteInfos;
    public $pagesys;
    public $pointSiteCode;

    protected function getSiteInfo()
    {
        if (!empty($this->pointSiteCode)) {
            //var_dump($this->pointSiteCode);exit();
            return $this->siteInfos[$this->pointSiteCode];
        }
        return $this->siteInfos[$this->pagesys['domain_code']];
    }

    public function _aboutusPagesys($mobileUrl = false)
    {
        $domain = $this->getDomainUrl($mobileUrl);
        return empty($domain) ? '' : $domain . '/' . $this->pagesys['keyparam'] . '.html';
    }

    protected function _articleUrl($mobileUrl = false)
    {
    }

    protected function getDomainUrl($mobile = false)
    {
        if ($mobile) {
            return isset($this->siteInfo['domains']['m']) ? $this->siteInfo['domains']['m'] : '';
        }
        return isset($this->siteInfo['domains']['pc']) ? $this->siteInfo['domains']['pc'] : '';
    }
}
