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
use Swoolecan\Baseapp\Exceptions\BusinessException;

class PermissionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = 'aaa';//$request->token;
        $passportBase = make(PassportBaseService::class);
        $permission = $passportBase->checkPermission($token);
        if (!isset($permission['code']) || $permission['code'] != 200) {
            $message = isset($permission['message']) ? $permission['message'] : '无权进行该操作';
            $code = isset($permission['code']) ? $permission['code'] : 403;
            throw new BusinessException($code, $message);
        }
        return $handler->handle($request);
    }

}
