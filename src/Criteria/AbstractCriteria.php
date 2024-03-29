<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Criteria;

use Framework\Baseapp\Contracts\CriteriaInterface;
use Framework\Baseapp\Contracts\RepositoryInterface;
use Swoolecan\Foundation\Criteria\TraitCriteria;

abstract class Criteria implements CriteriaInterface
{
    use TraitCriteria;

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
}
