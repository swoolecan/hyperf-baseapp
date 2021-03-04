<?php

declare(strict_types = 1);

/**
 * This file is an abstract controller for hyperf
 *
 * @link     http://http://home.canliang.wang/
 * @document http://wiki.canliang.wang/
 * @contact  iamwangcan@gmail.com
 * @license  https://github.com/swoolecan/hyperf-baseapp/blob/master/LICENSE.md
 */

namespace Swoolecan\Baseapp\Models;

use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;
use Swoolecan\Baseapp\Helpers\ResourceContainer;
use Swoolecan\Baseapp\Models\Traits\Rest;

abstract class AbstractModel extends BaseModel
{
    use Rest;

    /**
     * @Inject                
     * @var ResourceContainer
     */
    protected $resource;

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    public static $status = [
        0 => '禁用',
        1 => '正常'
    ];
    protected $dateFormat = 'Y-m-d H:i:s';

    const STATUS_DELETED = -1; //status 为-1表示删除
    const STATUS_DISABLE = 0; //status 为0表示未启用
    const STATUS_ENABLE = 1; //status 为1表示正常
    
    public function getPointModel($code)
    {
        return $this->resource->getObject('model', $code);
    }

    function getFormatState($key = 0, $enum = array(), $default = '')
    {
        return array_key_exists($key, $enum) ? $enum[$key] : $default;
    }

    public function getColumnElems($type = 'keyValue')
    {
        $results = $this->getConnection()->getSchemaBuilder()->getColumnTypeListing($this->getTable());
        $datas = [];
        if ($type == 'keyValue') {
            $datas = [];
            foreach ($results as $result) {
                $datas[$result['column_name']] = empty($result['column_comment']) ? $result['column_name'] : $result['column_comment'];
            }
            return $datas;
        }
        return $results;
    }

    /*protected $attributes = [
        'status' => 1,
    ];

    public function getStatusTextAttribute()
    {
        return $this->attributes['status_text'] = $this->getFormatState($this->attributes['status'], self::$status);
    }*/

    /*public function getList(array $params, int $pageSize)
    {
        $params = [
            'sort_name' => 'id',
            'sort_value' => 'desc',
        ];
        $list = $this->query()->orderBy($params['sort_name'], $params['sort_value'])->paginate($pageSize);
        foreach ($list as &$value) {
            $value->sex_text;
            $value->status_text;
        }
        return $list;
    }*/

    public function fieldTypes()
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'last_at' => 'timestamp',
            'status' => 'checkbox',
            'type' => 'dropdown',
        ];
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }

    public function saved(Saved $event)
    {
        //$this->setCreatedAt('2019-01-01');
    }

    public function saving(Saving $event)
    {
        //$this->setCreatedAt('2019-01-01');
    }

    public function getParentField($keyField = 'id')
    {
        return "parent_{$keyField}";
    }

    public function getParentFirstValue($keyField = 'id')
    {
        return $keyField == 'id' ? 0 : '';
    }

    public function getKeyField()
    {
        return $this->getKeyName();
    }

    public function getNameField()
    {
        return 'name';
    }

    public function getLevelInfos($level, $parentValue = null, $datas = [])
    {
        if (empty($level)) {
            return $datas;
        }

        $keyField = $this->getKeyName();
        $parentField = $this->getParentField($keyField);
        $parentValue = is_null($parentValue) ? $this->getParentFirstValue($keyField) : $parentValue;
        $infos = $this->where($parentField, $parentValue)->get();
        $datas = array_merge($datas, (array) $infos);
        if ($level) {
            foreach ($infos as $info) {
                $datas = $this->getLevelInfos($level - 1, $info[$parentField], $datas);
            }

        }
        return $datas;
    }

    public function canDelete()
    {
        return true;
    }
}
