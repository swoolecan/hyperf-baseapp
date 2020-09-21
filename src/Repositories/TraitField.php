<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

trait TraitField
{
    public function getAttributeNames($scene = null)
    {
        $datas = array_merge($this->model->getColumnElems(), $this->extAttributeNames());
        if (is_null($scene)) {
            return $datas;
        }
        $fields = $this->getSceneFields($scene);
        if (empty($fields)) {
            return $datas;
        }
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $datas[$field] ?? $field;
        }
        return $result;
    }

    public function fieldFormElems()
    {
        return array_merge($this->extFieldFormElems(), [
        ]);
    }

    protected function updateFields()
    {
        return array_keys($this->fieldFormElems());
    }

    protected function createFields()
    {
        return array_keys($this->fieldFormElems());
    }

    protected function extFieldFormElems()
    {
        return [];
    }

    protected function extAttributeNames()
    {
        return [];
    }

    public function getSceneFields($scene = null)
    {   
        $fields = $this->_sceneFields();  
        if (is_null($scene)) {
            return $fields;
        }

        if (isset($fields[$scene])) {
            return $fields[$scene];
        }
        return [];
    }

    protected function _sceneFields()
    {
        return [];
    }

    public function getShowFields()
    {
        return [];
    }

    public function getKeyValues($elem, $value = null)
    {
        $method = "_{$elem}KeyDatas";
        $datas = $this->$method();
        if (is_null($value)) {
            return $datas;
        }
        if (isset($datas[$value])) {
            return $datas[$value];
        }
        return $value;
    }
}
