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
        $repositoryCode = !empty($this->pointRepository) ? $this->pointRepository : get_called_class();
        $this->repository = $resource->getObject('repository', $repositoryCode);
    }
}
