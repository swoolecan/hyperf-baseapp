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

abstract class AbstractModel extends BaseModel
{
    public static $status = [
        0 => '禁用',
        1 => '正常'
    ];

    const STATUS_DELETED = -1; //status 为-1表示删除
    const STATUS_DISABLE = 0; //status 为0表示未启用
    const STATUS_ENABLE = 1; //status 为1表示正常

    public function init($params = [])
    {
    }
    
    function getFormatState($key = 0, $enum = array(), $default = '')
    {
        return array_key_exists($key, $enum) ? $enum[$key] : $default;
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
}
