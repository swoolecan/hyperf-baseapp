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

namespace Framework\Baseapp\Models;

use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;
use Framework\Baseapp\Helpers\ResourceContainer;
use Swoolecan\Foundation\Models\TraitModel;

abstract class AbstractModel extends BaseModel
{
    use TraitModel;

    const STATUS_DELETED = -1; //status 为-1表示删除
    const STATUS_DISABLE = 0; //status 为0表示未启用
    const STATUS_ENABLE = 1; //status 为1表示正常

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
}
