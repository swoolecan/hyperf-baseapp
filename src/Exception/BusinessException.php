<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Exception;

use Throwable;
use Hyperf\Server\Exception\ServerException;
use Swoolecan\Baseapp\Constant\Code;

class BusinessException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = Code::getMessage($code);
        }

        parent::__construct($message, $code, $previous);
    }
}
