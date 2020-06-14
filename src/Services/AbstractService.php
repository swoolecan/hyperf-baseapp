<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Services;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Swoolecan\Baseapp\Repositories\AbstractRepository;

class AbstractService
{
    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    /** 
     * @var AbstractRepository
     */
    protected $noRepository;
    protected $repository;
    protected $resource;
    protected $pointRepository;

    /**
     * @param $resource
     */
    /*public function __construct($resource)
    {
        $this->resource = $resource;
        if (empty($this->noRepository)) {
            $this->repository = $resource->getObject('repository', get_called_class());
            $this->pointRepository = empty($pointRepository) ? $this->repository : $resource->getObject('repository', $repositoryCode);
        }
    }*/

    public function __call($name, $arguments)
    {   
        return $this->repository->{$name}(...$arguments);
    }

    public function getTreeInfos()
    {
    }
}
