<?php

declare(strict_types=1);

namespace Swoolecan\Baseapp\Exceptions;

use Swoolecan\Constants\Code;
use Hyperf\Server\Exception\ServerException;
use Throwable;

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
