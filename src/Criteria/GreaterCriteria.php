<?php 
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\CriteriaInterface;
use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;
use Swoolecan\Baseapp\Contracts\RepositoryInterface;

class GreaterCriteria extends Criteria
{
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, Repository $repository)
    {
        $field = $this->getField();
        if (empty($field)) {
            return $query;
        }
        $operator = isset($this->params['equal']) ? '>=' : '>';
        $value = $this->params['value'];
        $query->where($field, $operator, $value);
        //echo $query->toSql() . '=======';

        return $query;
    }
}
