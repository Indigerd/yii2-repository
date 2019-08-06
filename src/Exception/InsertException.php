<?php

namespace Indigerd\Repository\Exception;

use Throwable;

class InsertException extends \Exception
{
    protected $data;

    protected $collectionName;

    public function __construct(array $data, string $collectionName, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->data = $data;
        $this->collectionName = $collectionName;
        parent::__construct($message, $code, $previous);
    }
}
