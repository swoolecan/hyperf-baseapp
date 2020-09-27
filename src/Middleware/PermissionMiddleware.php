<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Middleware;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoolecan\Baseapp\Helper\SysOperation;
use Swoolecan\Baseapp\RpcClient\PassportRpcClient;
use Swoolecan\Baseapp\Exceptions\BusinessException;

class PermissionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $passportBase = make(PassportRpcClient::class);
        $dispatcher = $request->getAttribute('Hyperf\HttpServer\Router\Dispatched');
        $routeCode = $dispatcher->handler->options['routeCode'];
        $permission = $passportBase->checkPermission($userId, $routeCode);
        if (!isset($permission['code']) || $permission['code'] != 200) {
            $message = isset($permission['message']) ? $permission['message'] : '无权进行该操作';
            $code = isset($permission['code']) ? $permission['code'] : 403;
            throw new BusinessException($code, $message);
        }
        return $handler->handle($request);
    }

}
