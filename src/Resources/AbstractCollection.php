<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Resources;

use Hyperf\Utils\Collection;
use Fangx\Resource\Json\ResourceCollection;
use Swoolecan\Foundation\Resources\TraitCollection;

class AbstractCollection extends ResourceCollection
{
    use TraitCollection;

    /**
     * Map the given collection resource into its individual resources.
     *
     * @param mixed $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if ($resource instanceof MissingValue) {
            return $resource;
        }

        if (is_array($resource)) {
            $resource = new Collection($resource);
        }

        $collects = $this->collects();

        $this->collection = $collects && ! $resource->first() instanceof $collects
            ? $resource->mapInto($collects)
            : $resource->toBase();
        foreach ($this->collection as $collection) {
            $collection->setScene($this->getScene());
            $collection->setRepository($this->repository);
            $collection->setSimpleResult($this->simpleResult);
        }

        return $this->isPaginatorResource($resource)
            ? $resource->setCollection($this->collection)
            : $this->collection;
    }
}
