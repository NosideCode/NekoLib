<?php declare(strict_types=1);
namespace Neko;

use Exception;
use Throwable;

/**
 * Thrown when an invoked method is not supported or its functionality is not available.
 */
class UnsupportedOperationException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
