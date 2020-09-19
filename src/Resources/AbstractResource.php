<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Resources;

use Fangx\Resource\Json\JsonResource;

class AbstractResource extends JsonResource
{
    protected $_scene;
    protected $_repository;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
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

    protected function _listArray()
    {
        $datas = [];
        $fields = $this->_showFields();
        foreach ($fields as $field => $data) {
            $type = $data['type'] ?? 'common';
            $valueType = $data['valueType'] ?? 'self';
            $datas[$field] = array_merge($data, [
                'value' => $this->getValue($field, $valueType),
                'type' => $type,
            ]);
        }

        return $datas;
    }

    protected function getValue($field, $valueType)
    {
        switch ($valueType) {
        case 'key':
            return $this->getRepository()->getKeyValues($field, $this->$field);
        case 'relate':
        default:
            return $this->$field;
        }
    }
}
