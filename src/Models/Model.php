<?php

declare(strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Swoolecan\Baseapp\Model;

use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

abstract class Model extends BaseModel implements CacheableInterface
{
    use Cacheable;

    public static $status = [
        0 => '禁用',
        1 => '正常'
    ];

    const STATUS_DELETED = -1; //status 为-1表示删除
    const STATUS_DISABLE = 0; //status 为0表示未启用
    const STATUS_ENABLE = 1; //status 为1表示正常
    
    function getFormatState($key = 0, $enum = array(), $default = '')
    {
        return array_key_exists($key, $enum) ? $enum[$key] : $default;
    }
}
