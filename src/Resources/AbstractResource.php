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
        return $this->_formatShowFields('list');
    }

    protected function _formatShowFields($scene)
    {
        $fields = $this->getRepository()->getSceneFields($scene);
        $showFields = $this->getRepository()->getShowFields();
        $datas = [];
        foreach ($fields as $field) {
            if (!isset($showFields[$field])) {
                $datas[$field] = ['showType' => 'common', 'value' => $this->$field, 'valueSource' => $this->$field];
                continue ;
            }

            $data = $showFields[$field];
            $data['type'] = $data['type'] ?? 'common';
            $valueType = $data['valueType'] ?? 'self';
            if ($valueType == 'key') {
                $value = $this->getRepository()->getKeyValues($field, $this->$field);
            } elseif ($valueType == 'point') {
                $relate = $data['resource'];
                $value = $relate ? $this->$relate->$field : $this->$field;
            } else {
                $value = $this->$field;
            }
            $data['value'] = $value;
            $data['valueSource'] = $this->$field;
            $datas[$field] = $data;
        }

        return $datas;
    }
}
