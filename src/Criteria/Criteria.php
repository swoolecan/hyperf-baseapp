<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Criteria;

use Framework\Baseapp\Contracts\CriteriaInterface;
use Framework\Baseapp\Contracts\RepositoryInterface;

abstract class Criteria implements CriteriaInterface
{
    protected $field;
    protected $value;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param $query
     * @param RepositoryInterface $repository
     * @param array $params
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        return  $this->_pointApply($query, $repository);
    }

    public function _pointApply($query, $repository)
    {
        return $query;
    }

    public function getField()
    {
        return isset($this->params['field']) ? $this->params['field'] : false;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function _applyBase($query, $repository)
    {
        $field = $this->getField();
        if (empty($field)) {
            return $query;
        }
        $value = $this->params['value'];
        $operator = $this->params['operator'];
        $query->where($field, $operator, $value);

        return $query;
    }
}
