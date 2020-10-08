<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Repositories;

trait TraitSearchField
{
    public function getDealSearchFields($scene, $params)
    {
        $fields = $this->getSceneFields($scene . 'Search');
        if (empty($params) || empty($fields)) {
            return $this;
        }
        $defaultSearchFields = $this->getDefaultSearchDealFields();
        $showFields = $this->getSearchDealFields();
        $datas = [];
        foreach ($fields as $field) {
            $defaultSearchField = $defaultSearchFields[$field] ?? [];
            $showField = $showFields[$field] ?? [];
            $data = array_merge($defaultSearchField, $showField);
            if (!isset($params[$field]) && !isset($data['value'])) {
                continue;
            }
            $data['field'] = $data['field'] ?? $field;
            $data['operator'] = $data['operator'] ?? '=';
            $data['value'] = isset($params[$field]) ? $params[$field] : $data['value'];
            //print_r($data);
            //$datas[$field] = $data;
            $type = $data['type'] ?? 'common';
            $type = ucfirst($type);

            $criteriaClass = "\Swoolecan\Baseapp\Criteria\\{$type}Criteria";
            $this->pushCriteria(new $criteriaClass($data));
            //$repository->pushCriteria($criteria);
        }

        return $this;
    }

    public function getDefaultSearchDealFields()
    {
        return [
            'status' => [],
            'user_id' => [],
            'name' => ['operator' => 'like'],
            'region_code' => [],
            'start_at' => ['operation' => '>=', 'field' => 'created_at'],
            'end_at' => ['operation' => '<', 'field' => 'created_at'],
        ];
    }

    public function getSearchDealFields()
    {
        return [];
    }

    public function getFormatSearchFields($scene)
    {
        $fields = $this->getSceneFields($scene);
        $defaultSearchFields = $this->getDefaultSearchFields();
        $formFields = $this->getSearchFields();
        $fieldNames = $this->getAttributeNames($scene);
        $datas = [];
        foreach ($fields as $field) {
            $defaultSearchField = $defaultSearchFields[$field] ?? [];
            $formField = $formFields[$field] ?? [];
            $data = array_merge($defaultSearchField, $formField);
            $data = empty($data) ? ['type' => 'input'] : $data;
            if (in_array($data['type'], ['radio', 'selelct']) && !isset($data['infos'])) {
                $data['infos'] = $this->getKeyValues($field);
            }
            $data['label'] = $fieldNames[$field] ?? $field;
            $datas[$field] = $data;
        }

        return $datas;
    }

    public function getDefaultSearchFields()
    {
        return [
            'nickname' => ['type' => 'input', 'require' => ['add']],
            'user_id' => ['type' => 'selectSearch', 'require' => ['add'], 'searchResource' => 'user', 'searchApp' => 'passport'],
            'status' => ['type' => 'radio'],
            'area' => ['type' => 'cascader'],
        ];
    }

    public function getSearchFields()
    {
        return [];
    }
}
