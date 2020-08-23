<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resources;

use Fangx\Resource\Json\ResourceCollection;

class AbstractCollection extends ResourceCollection
{
    protected $_scene = 'list';

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     */
    /*public function __construct($resource, $scene)
    {
        parent::__construct($resource);

        $this->setScene($scene);
    }*/

    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray() :array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }

    public function setScene($scene = 'list')
    {
        $this->_scene = $scene;
    }

    public function getScene()
    {
        return $this->_scene;
    }

    /*public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'nickname' => $item->name,
                'email' => $item->email,
            ];
        })->all();
    }*/

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
        echo 'ttttssss';
        echo get_class($this->collection->first()) . 'uuuuuuuuuoooo' . $this->getScene();
        foreach ($this->collection as $collection) {
            echo get_class($collection) . 'yyyy';
            $collection->setScene($this->getScene());
        }

        return $this->isPaginatorResource($resource)
            ? $resource->setCollection($this->collection)
            : $this->collection;
    }
}
