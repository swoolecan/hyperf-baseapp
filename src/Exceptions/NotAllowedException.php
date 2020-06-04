<?php

declare(strict_types = 1);

namespace Swoolecan\Baseapp\Exceptions;

use Hyperf\Server\Exception\ServerException;

class NotAllowedException extends ServerException
{

    protected $code = 405;

    public function __construct(string $message = "")
    {
        parent::__construct($message, $this->code);
    }

}
