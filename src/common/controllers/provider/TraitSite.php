<?php
namespace common\controllers\provider;

use Yii;

trait TraitSite
{
	public function getSiteCode()
	{
		return $this->siteInfo['code'];
	}

    public function getRelateDomain($isMobile = null)
    {
        if (empty($this->siteInfoRelate)) {
            return '';
        }
        return $this->getCurrentDomain($isMobile, $this->siteInfoRelate['code']);
    }

	public function getCurrentDomain($isMobile = null, $pointCode = null)
	{
        $siteInfo = !empty($pointCode) ? $this->siteInfos[$pointCode] : $this->siteInfo;
		$isMobile = is_null($isMobile) ? $this->isMobile : $isMobile;
        if (empty($isMobile)) {
            return $siteInfo['domains']['pc'];
        }
        if (in_array($siteInfo['client_sort'], [0, 1])) {
            return $siteInfo['domains']['pc'];
        }
        return $siteInfo['domains']['m'];
	}

	public function getSiteInfo()
	{
		static $data;
		if (!is_null($data)) {
			return $data;
		}
        $siteInfos = (array) $this->siteInfos;
        foreach ($siteInfos as $siteCode => $info) {
			foreach ($info['domains'] as $client => $domain) {
                if ($this->mappingHost($domain)) {
                    $data = $info;
                    $data['client'] = $client;
                    $this->siteInfoRelate = !empty($info['code_relate']) ? $this->siteInfos[$info['code_relate']] : [];
                    //print_r($data);exit();

					return $data;
				}
            }
        }
	}

	public function getSiteInfos()
	{
		static $datas;
		if (!is_null($datas)) {
			return $datas;
		}
		return $this->getRuntimeParams('site-domain');
	}

	public function getPagesysElem($elem)
	{
		$data = $this->pagesysInfo;
		$str = isset($data[$elem]) ? $data[$elem] : '';
		return $str;
	}

	public function getSiteElem($elem)
	{
		$data = empty($this->siteInfoHide) ? $this->siteInfo : $this->siteInfoHide;
        $elem = $elem == 'nameBase' ? 'name_base' : $elem;
		$str = isset($data[$elem]) ? $data[$elem] : '';
		if ($elem == 'icp') {
			$str = "<a href='http://beian.miit.gov.cn' target='_blank' rel='nofollow'>{$str}</a>";
		}
		return $str;
	}

	protected function mappingHost($domain)
	{
		if ($domain == $this->host) {
			return true;
		}
		if (strpos($domain, '<') === false) {
			return false;
		}
		if (strpos($domain, '>') === false) {
			return false;
		}
        //var_dump($domain);
		$pattern = '/<.*>/iU';
		$replace = '[a-z]+';
		$patternNew = preg_replace($pattern, $replace, $domain);
        $patternNew = str_replace('http://', '', $patternNew);
        $patternNew = "/{$patternNew}/iU";
        //var_dump($patternNew);
		if (preg_match($patternNew, $this->host, $result)) {
			return true;
		}
		return false;
	}

    protected function getPagesysDatas($appcode, $index = null)
    {
        static $datas;
        if (!isset($datas[$appcode])) {
		    $datas[$appcode] = $this->getRuntimeParams('pagesys-' . $appcode);
        }
        if (is_null($index)) {
            return $datas[$appcode];
        }
        return isset($datas[$appcode][$index]) ? $datas[$appcode][$index] : [];
    }

    public function _infocmsCode()
    {
        return '';
    }

    public function _pagesysCode()
    {
        return '';
    }

    public function formatPagesys()
    {
        $siteInfo = $this->siteInfo;
        if (!isset($this->pagesysInfo['code'])) {
            return ;
        }
        $pCode = $this->pagesysInfo['code'];
        $pagesys = $this->getPagesysDatas($this->_pagesysCode(), $pCode);

        if (empty($pagesys)) {
            $tdkStr = $this->getSiteElem('name');
            $tdkInfo = ['title' => $tdkStr, 'keyword' => $tdkStr, 'description' => $tdkStr];
            $this->pagesysInfo['tdkInfo'] = $tdkInfo;
            return ;
        }
        $pagesys = array_merge($this->pagesysInfo, $pagesys);

        if (!isset($pagesys['pcMappingUrl'])) {
            $pcUrl = $siteInfo['domains']['pc'];
            $pcUrl .= $pagesys['client_sort'] == 3 ? $this->clientUrl : '';
            $pagesys['pcMappingUrl'] = $pcUrl;
        }
        if (!isset($pagesys['mobileMappingUrl'])) {
            $mobileUrl = isset($siteInfo['domains']['m']) ? $siteInfo['domains']['m'] : $siteInfo['domains']['pc'];
            $mobileUrl .= $pagesys['client_sort'] == 3 ? $this->clientUrl : '';
            $pagesys['mobileMappingUrl'] = $mobileUrl;
        }

        $tdkData = isset($this->pagesysInfo['tdkData']) ? $this->pagesysInfo['tdkData'] : [];
		$page = $this->getInputParams('page');
        $page = str_replace('_', '', $page);
		$tdkData['{{PAGESTR}}'] = '';
		if ($page > 1) {
			$tdkData['{{PAGESTR}}'] = "_第{$page}页";
		}

        $baseWilds = ['{{CITYNAME}}', '{{SITENAME}}'];
        $cityName = $this->currentCityName;
        $baseWildValues = [$cityName, $this->getSiteElem('name')];
        $placeholder = array_merge($baseWilds, array_keys($tdkData));
        $replace = array_merge($baseWildValues, array_values($tdkData));
        $tdkInfo = [];
        foreach (['title', 'keyword', 'description'] as $key) {
            $value = $pagesys[$key];
            $tdkInfo[$key] = str_replace($placeholder, $replace, $value);
        }
        $pagesys['tdkInfo'] = $tdkInfo;

        $this->pagesysInfo = $pagesys;
        return true;

	    //$tdkInfo = []; $currentPage; $currentElem; $currentSubElem; $pagePosition; $pagePositionName;
	    //$templateCode = ''; $pcMappingUrl; $mobileMappingUrl;
    }

	public function getCompanyHots($code)
	{
		return $this->getCompanyInfos($code, [2]);
	}

    public function getCompanyRuns($code)
    {
		return $this->getCompanyInfos($code, [1, 2]);
	}

	public function getCompanyAll($code)
	{
		return $this->getCompanyInfos($code, 'all');
	}

	public function getCompanyInfos($code, $where)
	{
		$where = is_null($where) ? 'all' : $where;
		$params = [
			'indexBy' => 'code', 
			'orderBy' => ['orderlist_' . $code => SORT_DESC]
		];
		if ($where != 'all') {
			$field = "status_{$code}";
			$params['where'] = [$field => $where];
		}
		return $this->getPointModel('company')->getInfos($params);
    }

	public function getCompanySorts($code, $where)
	{
        $datas = $this->getCompanyInfos($code, $where);
        $formatDatas = [];
        foreach ($datas as $info) {
        	$formatDatas[$info['code_first']][] = $info;
        }
        ksort($formatDatas);
		return $formatDatas;
	}

	protected function getPointCompany($code = null, $byIp = false)
	{
		$model = $this->getPointModel('company');
		$company = $model->getInfo($code, 'code');
		$company = empty($company) && $byIp ? $model->getInfoByIp() : $company;
		$company = empty($company) ? $model->getInfo($this->defaultCity, 'code') : $company;
		return $company;
	}

    protected function getCurrentCompany($code = null)
    {
        $code = is_null($code) ? $this->getInputParams('city') : $code;
        $session = Yii::$app->session;
        $company = isset($session['current_company']) ? $session['current_company'] : [];
        $currentCode = isset($company['code']) ? $company['code'] : '';
        if (!empty($currentCode) && (empty($code) || $currentCode == $code)) {
			return $this->initCity($company);
        }

		$company = $this->getPointCompany($code, true);
        $session['current_company'] = $company;
		return $this->initCity($company);
    }

    public function initCity($company)
    {
        $this->currentCityCode = $company['code'];
        $this->currentCityName = $company['name'];
		return $company;
    }

	public function getDefaultCity()
	{
		return 'beijing';
	}
}
