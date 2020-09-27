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

    public function getDefaultShowFields()
    {
        return [
            'status' => ['valueType' => 'key'],
            'user_id' => ['valueType' => 'point', 'relate' => 'user'],
        ];
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

    public function getFormatFormFields($scene)
    {
        $fields = $this->getSceneFields($scene);
        $defaultFormFields = $this->getDefaultFormFields();
        $formFields = $this->getFormFields();
        $datas = [];
        foreach ($fields as $field) {
            $defaultFormField = $defaultFormFields[$field] ?? [];
            $formField = $formFields[$field] ?? [];
            $data = array_merge($defaultFormField, $formField);
            $datas[$field] = empty($data) ? ['type' => 'input'] : $data;
        }

        return $datas;
    }

    public function getFormatShowFields($scene, $model)
    {
        $fields = $this->getSceneFields($scene);
        $defaultShowFields = $this->getDefaultShowFields();
        $showFields = $this->getShowFields();
        $datas = [];
        foreach ($fields as $field) {
            $value = $model->$field;
            $defaultShowField = $defaultShowFields[$field] ?? [];
            $showField = $showFields[$field] ?? [];
            $data = array_merge($defaultShowField, $showField);
            if (!empty($data)) {
                $datas[$field] = ['showType' => 'common', 'value' => $value, 'valueSource' => $value];
                continue ;
            }

            $data['showType'] = $data['showType'] ?? 'common';
            $valueType = $data['valueType'] ?? 'self';

            if ($valueType == 'key') {
                $value = $this->getKeyValues($field, $model->$field);
            } elseif ($valueType == 'point') {
                $relate = $data['relate'];
                $relate = $relate ? $model->$relate : false;
                $relateField = $data['relateField'] ?? 'name';
                print_r($relate);
                $value = $relate ? $relate->$relateField : $value;
            } elseif ($valueType == 'pointCache') {
                $relate = $data['relate'];
                $relate = $relate ? $model->$relate : false;
                $relateField = $data['relateField'] ?? 'name';
                $value = $relate ? $relate->$relateField : $value;
            }
            $data['value'] = $value;
            $data['valueSource'] = $value;
            $datas[$field] = $data;
        }

        return $datas;
    }

    public function getDefaultFormFields()
    {
        return [
            'nickname' => ['type' => 'input', 'require' => ['add']],
            'user_id' => ['type' => 'selectSearch', 'require' => ['add'], 'searchResource' => 'user', 'searchApp' => 'passport'],
            'status' => ['type' => 'radio', 'infos' => $this->getKeyValues('status')],
        ];
    }
}
