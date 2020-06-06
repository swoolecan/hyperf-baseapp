<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Criteria;

use Swoolecan\Baseapp\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{
    /**
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}
