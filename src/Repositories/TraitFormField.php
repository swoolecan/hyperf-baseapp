<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

trait TraitFormField
{
    public function getFormatFormFields($scene)
    {
        $fields = $this->getSceneFields($scene);
        $defaultFormFields = $this->getDefaultFormFields();
        $formFields = $this->getFormFields();
        $fieldNames = $this->getAttributeNames($scene);
        $datas = [];
        foreach ($fields as $field) {
            $defaultFormField = $defaultFormFields[$field] ?? [];
            $formField = $formFields[$field] ?? [];
            $data = array_merge($defaultFormField, $formField);
            $data = empty($data) ? ['type' => 'input'] : $data;
            if (in_array($data['type'], ['radio', 'selelct']) && !isset($data['infos'])) {
                $data['infos'] = $this->getKeyValues($field);
            }
            $data['label'] = $fieldNames[$field] ?? $field;
            $datas[$field] = $data;
        }

        return $datas;
    }

    public function getDefaultFormFields()
    {
        return [
            'nickname' => ['type' => 'input', 'require' => ['add']],
            'user_id' => ['type' => 'selectSearch', 'require' => ['add'], 'searchResource' => 'user', 'searchApp' => 'passport'],
            'status' => ['type' => 'radio'],
            'area' => ['type' => 'cascader'],
        ];
    }

    public function getFormFields()
    {
        return [];
    }
}