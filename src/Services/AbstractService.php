<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Services;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Swoolecan\Baseapp\Repositories\AbstractRepository;

class AbstractService
{
    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    /** 
     * @var AbstractRepository
     */
    protected $noRepository;
    protected $repository;
    protected $resource;
    protected $pointRepository;

    /**
     * @param $resource
     */
    /*public function __construct($resource)
    {
        $this->resource = $resource;
        if (empty($this->noRepository)) {
            $this->repository = $resource->getObject('repository', get_called_class());
            $this->pointRepository = empty($pointRepository) ? $this->repository : $resource->getObject('repository', $repositoryCode);
        }
    }*/

    public function __call($name, $arguments)
    {   
        return $this->repository->{$name}(...$arguments);
    }

    public function getTreeInfos()
    {
    }

    protected function _writeLog($return, $mobile, $content, $sort, $startTime)
    {
        $endTime = microtime(true);
        $timeUsed = number_format($endTime - $startTime, 3);
        $currentDate = date('Y-m-d H:i:s');
        $content = "==={$mobile}==={$currentDate}===\r\n"
            . "---{$timeUsed}---{$return['message']}---{$return['extinfo']}---\r\n"
            . "---{$content}---\r\n\r\n";

        $logFile = \Yii::getAlias('@runtime') . '/logs/sms/' . date('Y-m-d') . '/' . $sort;
        $logFile .= $return['status'] ? '_success.log' : '_error.log';
        $path = dirname($logFile);
        if (!is_dir($path)) {
            \yii\helpers\FileHelper::createDirectory($path);
        }
        file_put_contents($logFile, $content, FILE_APPEND);

        return true;
    }
}
