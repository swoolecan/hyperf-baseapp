<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resources;

use Fangx\Resource\Json\JsonResource;

class AbstractResource extends JsonResource
{
    protected $_scene;
    protected $_repository;
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $scene = $this->getScene();
        $method = "_{$scene}Array";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return $this->_keyvalueArray();
    }

    public function setRepository($repository)
    {
        $this->_repository = $repository;
    }

    public function getRepository()
    {
        return $this->_repository;
    }

    public function setScene($scene)
    {
        $this->_scene = $scene;
    }

    public function getScene()
    {
        return $this->_scene;
    }

    protected function _keyvalueArray()
    {
        $keyField = $this->resource->getKeyName();
        return [
            $keyField => $this->$keyField,
            'name' => $this->name,
        ];
    }

    protected function _listArray()
    {
        return $this->getRepository()->getFormatShowFields('list', $this->resource);
    }
}
