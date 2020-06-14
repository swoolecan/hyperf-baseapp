<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Middlewares;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoolecan\Baseapp\Helpers\SysOperation;

class InitResourceMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Context::set('resources', SysOperation::initResourceDatas());
        return $handler->handle($request);
    }

}
