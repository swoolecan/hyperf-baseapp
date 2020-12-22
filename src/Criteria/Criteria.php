<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{
    protected $field;
    protected $value;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param $query
     * @param Repository $repository
     * @param array $params
     * @return mixed
     */
    public abstract function apply($query, Repository $repository);

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
