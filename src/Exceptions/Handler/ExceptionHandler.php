<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Exceptions\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler as ExceptionHandlerBase;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use Hyperf\Di\Annotation\Inject;
use Swoolecan\Baseapp\Helpers\Helper;
use Swoolecan\Baseapp\Constants\Code;

class ExceptionHandler extends ExceptionHandlerBase
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Inject
     * @var Helper
     */
    protected $helper;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        //echo get_class($throwable);
        $this->stopPropagation();

        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());
        
        //$result = ['status' => $throwable->getCode(), 'message' => $throwable->getMessage()];
        if (property_exists($throwable, 'validator')) {
            $code = $throwable->status;
            $message = $throwable->validator->errors()->first();
        } else {
            $message = $throwable->getMessage();
            $code = $throwable->getCode();
        }

        $result = $this->helper->error($code, $message, $message);
        $return = $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withBody(new SwooleStream(json_encode($result, 256)));
        return $return;
    }

    /*public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $message = $throwable->validator->errors()->first();
        $errors = $throwable->validator->errors();
        $result = $this->helper->error($throwable->getCode(), $throwable->getMessage(), $throwable->getMessage());
        return $response->withStatus($throwable->getCode())
                        ->withAddedHeader('content-type', 'application/json')
                        ->withBody(new SwooleStream($this->helper->jsonEncode($result)));
    }*/

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
