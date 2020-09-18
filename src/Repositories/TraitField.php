<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

trait TraitField
{
    public function attributeNames()
    {
        return array_merge($this->model->getColumnElems(), $this->extAttributeNames());
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
