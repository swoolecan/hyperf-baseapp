<?php

namespace common\models\traits;

use Yii;

trait SearchBase
{
    public function getSearchAttributes($withParams = true, $pointFields = null)
    {
        $params = Yii::$app->request->getQueryParams();
        $elems = $this->searchModel->getSearchDatas(true);
        $return = [];
        foreach ($elems as $key => $elem) {
            if (!is_null($pointFields) && !in_array($key, $pointFields)) {
                continue;
            }
            $elem['value'] = $withParams && isset($params[$key]) ? $params[$key] : 'no';
            $return[$key] = $elem;
        }
        return $return;
    }

    public function getRelateAttributes($pointFields = null)
    {
        $elems = $this->searchModel->getSearchDatas(true);
        $return = [];
        foreach ($elems as $key => $elem) {
            if (!isset($elem['infos'])) {
                continue;
            }
            if (!is_null($pointFields) && !in_array($key, $pointFields)) {
                continue;
            }
            $infos = $elem['infos'];
            if (isset($infos['all-search'])) {
                unset($infos['all-search']);
            }
            $return[$key] = $infos;
        }
        return $return;
    }
}
