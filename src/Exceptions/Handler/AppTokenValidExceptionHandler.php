<?php

declare(strict_types = 1);

namespace App\Exception\Handler;

use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Phper666\JwtAuth\Exception\TokenValidException;
use Throwable;
use App\Helpers\Helper;
use App\Constants\Code;

class AppTokenValidExceptionHandler extends ExceptionHandler {

    /**
     * @Inject
     * @var Helper
     */
    protected $helper;

    public function handle(Throwable $throwable, ResponseInterface $response) {
        $this->stopPropagation();
        $result = $this->helper->error(Code::UNAUTHENTICATED, $throwable->getMessage());
        return $response->withStatus($throwable->getCode())
                        ->withAddedHeader('content-type', 'application/json')
                        ->withBody(new SwooleStream($this->helper->jsonEncode($result)));
    }

    public function isValid(Throwable $throwable): bool {
        return $throwable instanceof TokenValidException;
    }

}
