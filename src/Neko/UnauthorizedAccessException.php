<?php declare(strict_types=1);
namespace Neko;

use Exception;
use Throwable;

/**
 * Thrown when the current user does not have authorization to access a file or perform an action.
 */
class UnauthorizedAccessException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if ($message === '')
        {
            $message = 'Attempted to perform an unauthorized operation';
        }

        parent::__construct($message, $code, $previous);
    }
}
