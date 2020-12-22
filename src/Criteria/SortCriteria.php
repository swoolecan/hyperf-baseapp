<?php 
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\CriteriaInterface;
use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;
use Swoolecan\Baseapp\Contracts\RepositoryInterface;

class SortCriteria extends Criteria
{
    /**
     * @param $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, Repository $repository)
    {
        foreach ($this->params as $field => $sortType) {
            $sortType = in_array($sortType, ['asc', 'desc']) ? $sortType : 'desc';
            $query->orderBy($field, $sortType);
        }

        return $query;
    }
}
