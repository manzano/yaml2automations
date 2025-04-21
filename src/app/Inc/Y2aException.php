<?php
namespace Manzano\Yaml2Automations\Inc;

use RuntimeException;

class Y2aException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
