<?php

namespace common\models\traits;

use Yii;
use yii\data\ActiveDataProvider;

trait SearchSearch
{
	public $originParams;
	public $pageSize = 200;
    public $created_at_start;
    public $created_at_end;
	public $created_day_start;
	public $created_day_end;
	public $searchPointForm;

    public function search($params)
    {
        $query = self::find();
        $this->_searchPre($query);

		$this->load($params, '');
		$this->originParams = $params;
		$this->pageSize = isset($params['page_size']) ? intval($params['page_size']) : $this->pageSize;
		if ($this->pageSize < 1 || $this->pageSize > 500) {
			$this->pageSize = 200;
		}
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'sort' => $this->_defaultOrder(),
            'pagination' => $this->_defaultPagination(),
        ]);

        /*if (!($this->load($params, '') || !$this->validate())) {
            return $dataProvider;
		}*/

        $elems = $this->_searchElems();
        foreach ($elems as $elem) {
            $type = ucfirst($elem['type']);
            $method = "_search{$type}";
            $this->$method($query, $elem);
        }
		$privWhere = $this->dealPriv('listinfo');
		if ($privWhere !== true) {
			$query->andWhere($privWhere);
		}
		/*$ownerWhere = $this->dealOwnerPriv('listinfo');
		if ($ownerWhere !== true) {
			$query->andWhere($ownerWhere);
		}*/
		//print_r($query->createCommand()->getRawSql()); echo "\n";
        return $dataProvider;
    }

    protected function _searchPre(& $query)
    {
        return ;
    }
    
    protected function _searchWrapComma(& $query, $elem)
    {
        $field = $elem['field'];
        $values  = (array) $this->$field;
        foreach ($values as $value) {
			if (empty($value)) {
				continue;
			}
            $value = ',' . $value . ',';
            $query->orFilterWhere(['like', $field, $value]);
        }
    }

    protected function _searchCommon(& $query, $elem)
    {
        $field = $elem['field'];
        $value = isset($elem['value']) ? $elem['value'] : $this->$field;
		$pValue = is_array($value) ? $value : strval($value);
        if (is_null($value) || $pValue == 'all-search' || in_array('all-search', (array) $pValue, true)) {
            return ;
        }
        $sort = isset($elem['sort']) ? $elem['sort'] : '';
        switch ($sort) {
		case 'noEqual':
            $query->andFilterWhere(['!=', $field, $value]);
            break;
        case 'less':
            $query->andFilterWhere(['<', $field, $value]);
            break;
        case 'notIn':
            $query->andFilterWhere(['not in', $field, $value]);
            break;
        case 'like':
            $query->andFilterWhere(['like', $field, $value]);
            break;
		case 'orLike':
			if (isset($elem['relateField'])) {
			    $query->andFilterWhere(['or', ['like', $field, $value], ['like', $elem['relateField'], $value]]);
			} else {
			    $query->orFilterWhere(['like', $field, $value]);
			}
			break;
		case 'orEqual':
			if (is_array($value)) {
			$query->orWhere(['in', $field, $value]);
			} else {
			$query->orWhere(['=', $field, $value]);
			}
			break;
		case 'strict':
		    $query->andWhere([$field => $value]);
			break;
        default:
		    $query->andFilterWhere([$field => $value]);
        }
    }

    protected function _searchRangeTime(& $query, $elem)
    {
        $field = $elem['field'];
        $startAttr = $field . '_start';
        $endAttr = $field . '_end';
        $timestamp = isset($elem['timestamp']) ? $elem['timestamp'] : true;
        $startTime = $timestamp ? strtotime($this->$startAttr) : $this->$startAttr;
        $query->andFilterWhere(['>=', $field, (int) $startTime]);

        if ($this->$endAttr > 0) {
            $endTime = $timestamp ? strtotime($this->$endAttr) : $this->$endAttr;
            $query->andFilterWhere(['<=', $field, (int) $endTime]);
        }
    }

    protected function _defaultOrder()
    {
		$field = $this->hasProperty('orderlist') ? 'orderlist' : 'id';
        return [
			'sortParam' => 'sortfield',
            'defaultOrder' => [
                //$field => SORT_DESC,            
                'id' => SORT_DESC,            
            ]
        ];
    }

    protected function _defaultPagination()
    {
		return [
			'pageSize' => $this->pageSize,
		];
    }

	protected function _searchElems()
	{
		$elems = $this->_searchSourceElems();
		$fields = isset($elems['fields']) ? $elems['fields'] : [];
		$default = isset($elems['default']) ? $elems['default'] : [];
        return $this->_fieldFormatSearchElems($fields, $default);
	}

	protected function _searchSourceElems()
	{
		return [];
	}
}
