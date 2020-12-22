<?php 
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\CriteriaInterface;
use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;
use Swoolecan\Baseapp\Contracts\RepositoryInterface;

class CommonCriteria extends Criteria
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
        $operator = $this->params['operator'];
        $value = $this->params['value'];
        if ($operator == 'like-left') {
            $operator = 'like';
            $value = "%{$value}";
        } elseif ($operator == 'like-right') {
            $operator = 'like';
            $value = "{$value}%";
        } else if ($operator == 'like') {
            $value = "%{$value}%";
        }
        
        $query->where($field, $operator, $value);
        //echo $query->toSql() . '=======';

        return $query;
    }
}
