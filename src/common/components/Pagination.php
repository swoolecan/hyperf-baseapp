<?php

namespace common\components;

use Yii;
use yii\data\Pagination as PaginationBase;

class Pagination extends PaginationBase
{
    public $pagePreStr;
    public $noHost;
	public $_page;

    /**
     * Creates the URL suitable for pagination with the specified page number.
     * This method is mainly called by pagers when creating URLs used to perform pagination.
     * @param integer $page the zero-based page number that the URL should point to.
     * @param integer $pageSize the number of items on each page. If not set, the value of [[pageSize]] will be used.
     * @param boolean $absolute whether to create an absolute URL. Defaults to `false`.
     * @return string the created URL
     * @see params
     * @see forcePageParam
     */
    public function createUrl($page, $pageSize = null, $absolute = false)
    {
        $page = (int) $page;
        $pageSize = (int) $pageSize;
        if (($params = $this->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }
        if ($page > 0 || $page >= 0 && $this->forcePageParam) {
            $pageValue = $page + 1;
            $params[$this->pageParam] = $this->pagePreStr ? $this->pagePreStr . $pageValue : $pageValue;
        } else {
            unset($params[$this->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->getPageSize();
        }
        if ($pageSize != $this->defaultPageSize) {
            $params[$this->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pageSizeParam]);
        }
        $params[0] = $this->route === null ? Yii::$app->controller->getRoute() : $this->route;
        $urlManager = $this->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;
        if ($absolute) {
            $return = $urlManager->createAbsoluteUrl($params);
        } else {
            $return = $urlManager->createUrl($params);
        }

        if ($this->noHost) {
            $urlInfos = parse_url($return);
            $return = isset($urlInfos['path']) ? $urlInfos['path'] : '';
            $return .= isset($urlInfos['query']) ? $urlInfos['query'] : '';
        }

        return $return;
    }

    public function getPage($recalculate = false)
    {
        if ($this->_page === null || $recalculate) {
			$page = intval(Yii::$app->request->post($this->pageParam));
            $page = empty($page) ? $this->getQueryParam($this->pageParam, 1) : $page;
            if ($this->pagePreStr) {
                $page = str_replace($this->pagePreStr, '', $page);
            }
            $page = (int) $page - 1;
            $this->setPage($page, true);
        }

        return $this->_page;
    }
}
