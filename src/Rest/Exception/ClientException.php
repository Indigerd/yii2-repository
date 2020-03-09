<?php declare(strict_types=1);

namespace Indigerd\Repository\Rest\Exception;

use Throwable;

class ClientException extends \RuntimeException
{
    protected $httpCode;

    public function __construct(int $httpCode, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    public function getHttpCode(): int
    {
        return $this->code;
    }
}
