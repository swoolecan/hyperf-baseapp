<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Middleware;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoolecan\Baseapp\Helper\SysOperation;
use Swoolecan\Baseapp\JsonRpcClient\PassportBaseService;

class PermissionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $passportBase = make(PassportBaseService::class);
        $token = $request->token;
        return $handler->handle($request);
    }

}
