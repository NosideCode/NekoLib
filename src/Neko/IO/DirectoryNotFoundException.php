<?php declare(strict_types=1);
namespace Neko\IO;

use Throwable;

/**
 * Thrown when a directory is not found or does not exist.
 */
final class DirectoryNotFoundException extends IOException
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
