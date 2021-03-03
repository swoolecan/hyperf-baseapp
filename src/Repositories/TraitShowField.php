<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

use Hyperf\Utils\Str;

trait TraitShowField
{
    public function getFormatShowFields($scene, $model, $simple = false)
    {
        $fields = $this->getSceneFields($scene);
        $defaultShowFields = $this->getDefaultShowFields();
        $showFields = $this->getShowFields();
        $datas = [];
        foreach ($fields as $field) {
            if ($field == 'point_operation') {
                $datas[$field] = $this->getPointOperation($model, $field);
                continue;
            }
            $value = $model->$field;
            $defaultShowField = $defaultShowFields[$field] ?? [];
            $showField = $showFields[$field] ?? [];
            $data = array_merge($defaultShowField, $showField);
            if (empty($data)) {
                $datas[$field] = $simple ? $value : ['showType' => 'common', 'value' => $value, 'valueSource' => $value];
                continue ;
            }

            $data['valueSource'] = $value;
            $data['showType'] = $data['showType'] ?? 'common';
            $valueType = $data['valueType'] ?? 'self';

            if ($valueType == 'key') {
                $value = $this->getKeyValues($field, $model->$field);
            } elseif ($valueType == 'select') {
                $value = $this->getKeyValues($field);
            } elseif ($valueType == 'point') {
                $relate = $data['relate'];
                $relate = $relate ? $model->$relate : false;
                $relateField = $data['relateField'] ?? 'name';
                $value = $relate ? $relate->$relateField : $value;
            } elseif ($valueType == 'cache') {
                $relate = $data['relate'];
                $relate = $relate ? $this->get($relate) : false;
                $relateField = $data['relateField'] ?? 'name';
                $value = $relate ? $relate[$relateField] : $value;
            } elseif ($valueType == 'cacheOut') {
                $value = $this->getCacheOutData($data['app'], $data['relate'], $value, $data['keyField']);
            } elseif ($valueType == 'callback') {
                $method = $data['method'];
                $value = $this->$method($model, $field);
            } elseif ($valueType == 'datetime') {
                $value = $model->$field->toDateTimeString();
                $data['valueSource'] = $value;
            } elseif ($valueType == 'popover') {
                $strLen = $data['strLen'] ?? 1;
                $suffix = $strLen < Str::length($value) ? '...' : '';
                $value = Str::substr($value, 0, $strLen) . $suffix; 
            }
            $data['value'] = $value;
            $datas[$field] = $simple ? $value : $data;
        }

        return $datas;
    }

    public function getDefaultShowFields()
    {
        return [
            'status' => ['valueType' => 'key'],
            'orderlist' => ['showType' => 'edit'],
            'logo' => ['showType' => 'picture'],
            'thumb' => ['showType' => 'picture'],
            'created_at' => ['valueType' => 'datetime'],
            'updated_at' => ['valueType' => 'datetime'],
            'user_id' => ['valueType' => 'point', 'relate' => 'user'],
            'region_code' => ['valueType' => 'cacheOut', 'relate' => 'region', 'app' => 'passport', 'keyField' => 'code'],
        ];
    }

    public function getShowFields()
    {
        return [];
    }

    public function getPointOperation($model, $field)
    {
        return [
            'showType' => 'operation',
            'operations' => $this->_pointOperations($model, $field),
        ];
    }

    protected function _pointOperations($model, $field)
    {
        return [];
    }

    public function getHaveSelection($scene)
    {
        return false;
    }

    public function getSelectionOperations($scene)
    {
        return [];
    }
}
