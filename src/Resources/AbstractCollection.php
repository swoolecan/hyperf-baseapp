<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resources;

use Fangx\Resource\Json\ResourceCollection;

class AbstractCollection extends ResourceCollection
{
    protected $_scene = 'list';
    protected $_model;
    protected $repository;
    protected $params;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     */
    public function __construct($resource, $scene, $repository)
    {
        $this->setScene($scene);
        $this->repository = $repository;
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray() :array
    {
        $addFormFields = $this->repository->getFormatFormFields('add');
        $updateFormFields = $this->repository->getFormatFormFields('update');
        $searchFields = $this->repository->getFormatSearchFields($this->getScene() . 'Search');
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ],
            'fieldNames' => $this->repository->getAttributeNames($this->getScene()),
            'addFormFields' => $addFormFields ? $addFormFields : (object)[],
            'updateFormFields' => $updateFormFields ? $updateFormFields : (object)[],
            'searchFields' => $searchFields ? $searchFields : (object)[],
        ];
    }

    public function getModel()
    {
        return $this->_model ?? $this->collection->first();
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function setScene($scene = 'list')
    {
        $this->_scene = $scene;
    }

    public function getScene()
    {
        return $this->_scene;
    }

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
        }

        return $this->isPaginatorResource($resource)
            ? $resource->setCollection($this->collection)
            : $this->collection;
    }
}
