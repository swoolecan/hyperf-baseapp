<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Services;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Framework\Baseapp\Helpers\ResourceContainer;
use Framework\Baseapp\Repositories\AbstractRepository;
use Swoolecan\Foundation\Services\TraitService;

abstract class AbstractService
{
    use TraitService;

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @Inject                
     * @var ResourceContainer
     */
    public $resource;

    /** 
     * @var AbstractRepository
     */
    protected $repository;
    //protected $pointRepository;

    public function init()
    {
        if (empty($this->noRepository)) {
            $this->repository = $this->resource->getObject('repository', get_called_class());
            //$this->pointRepository = empty($pointRepository) ? $this->repository : $resource->getObject('repository', $repositoryCode);
        }
    }
}
