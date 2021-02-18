<?php 
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\CriteriaInterface;
use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;
use Swoolecan\Baseapp\Contracts\RepositoryInterface;

class MultipleCriteria extends Criteria
{
    /**
     * @param $query
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
        $mark = $this->params['mark'] ?? ',';
        $values = array_filter(explode($mark, $this->params['value']));
        foreach ($values as $value) {
            $query->orWhere($field, $operator, $value);
        }

        return $query;
    }
}
