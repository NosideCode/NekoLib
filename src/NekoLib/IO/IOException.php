<?php declare(strict_types=1);
namespace NekoLib\IO;

use Exception;
use Throwable;

/**
 * Thrown when an IO error occurs.
 */
class IOException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
