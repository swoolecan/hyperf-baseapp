<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Middleware;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoolecan\Baseapp\JsonRpcClient\PassportBaseService;
use Swoolecan\Baseapp\Exceptions\BusinessException;

class JWTUserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $jwtResult = $request->getAttribute('jwtResult');
        $passportBase = make(PassportBaseService::class);
        if (empty($jwtResult) || isset($jwtResult['user_id']) || empty($jwtResult['user_id'])) {
            throw new BusinessException(400, 'Token有误');
        }
        $user = $passportBase->getUserById($jwtResult['user_id']);
        if (!isset($permission['code']) || $permission['code'] != 200) {
            $message = isset($permission['message']) ? $permission['message'] : 'Token未验证通过';
            $code = isset($permission['code']) ? $permission['code'] : 401;
            throw new BusinessException($code, $message);
        }
        $request = $request->withAttribute('user', $user['data']);
        Context::set(ServerRequestInterface::class, $request);
        return $handler->handle($request);
    }

}