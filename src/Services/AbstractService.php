<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Services;

use Swoolecan\Baseapp\Repositories\AbstractRepository;

class AbstractService
{
    /** 
     * @var AbstractRepository
     */
    protected $repository;
    protected $resource;
    protected $pointRepository;

    /**
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->repository = $resource->getObject('repository', get_called_class());
        $this->pointRepository = empty($pointRepository) ? $this->repository : $resource->getObject('repository', $repositoryCode);
    }

    public function __call($name, $arguments)
    {   
        return $this->repository->{$name}(...$arguments);
    }
}
