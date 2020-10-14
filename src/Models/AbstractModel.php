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
use Swoolecan\Baseapp\Models\Traits\Rest;

abstract class AbstractModel extends BaseModel
{
    use Rest;

    public static $status = [
        0 => '禁用',
        1 => '正常'
    ];
    protected $dateFormat = 'Y-m-d H:i:s';

    const STATUS_DELETED = -1; //status 为-1表示删除
    const STATUS_DISABLE = 0; //status 为0表示未启用
    const STATUS_ENABLE = 1; //status 为1表示正常
    
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
                $datas[$result['COLUMN_NAME']] = empty($result['COLUMN_COMMENT']) ? $result['COLUMN_NAME'] : $result['COLUMN_COMMENT'];
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

    public function saved(Saved $event)
    {
echo 'sssssyyyy';
        //$this->setCreatedAt('2019-01-01');
    }

    public function saving(Saving $event)
    {
echo 'iiiiiiiiioooo';
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
}
