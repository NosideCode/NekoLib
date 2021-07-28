<?php declare(strict_types=1);
namespace Neko\Collections;

use Exception;
use Throwable;
use function is_object;

/**
 * Thrown when the given key is not found in the key/value list.
 */
class KeyNotFoundException extends Exception
{
    public function __construct(mixed $key, int $code = 0, Throwable $previous = null)
    {
        if (is_object($key))
        {
            $name = $key::class;
            $key = "object($name)";
        }

        $message = "The key \"$key\" was not found";
        parent::__construct($message, $code, $previous);
    }
}
