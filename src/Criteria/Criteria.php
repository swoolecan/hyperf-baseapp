<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{
    protected $params;

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
}
