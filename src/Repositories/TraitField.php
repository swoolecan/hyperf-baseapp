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
            'id' => [
                'type' => 'int',
                'value' => null,
            ],
        ]);
    }

    protected function extFieldFormElems()
    {
        return [];
    }

    protected function extAttributeNames()
    {
        return [];
    }
}
