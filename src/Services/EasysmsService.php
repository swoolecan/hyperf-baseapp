<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Services;

use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CachePut;
use Swoolecan\Foundation\Services\TraitEasysmsService;

class EasysmsService extends AbstractService
{
    use TraitEasysmsService;

    /**
     * @CachePut(prefix="sms-", group="filesys")
     */
    protected function cacheCode($key)
    {
        return $this->createInfo;
    }

    /**
     * @Cacheable(prefix="sms-", group="filesys")
     */
    protected function getCodeInfo($key)
    {
        return null;
    }
}
