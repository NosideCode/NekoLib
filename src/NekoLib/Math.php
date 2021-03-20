<?php declare(strict_types=1);
namespace NekoLib;

use InvalidArgumentException;
use OverflowException;

use function abs;
use function filter_var;
use function floor;
use function log10;
use function round;
use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Contains methods for performing basic numeric operations.
 */
final class Math
{
    /**
     * Clamps the given value between the given minimum and maximum values.
     *
     * @param int|float $value The value to be clamped.
     * @param int|float $min The lower bound of the result.
     * @param int|float $max The upper bound of the result.
     *
     * @return int|float
     * @throws InvalidArgumentException If the minimum value is greater than the maximum value.
     */
    public static function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        if ($min > $max)
        {
            throw new InvalidArgumentException("$min cannot be greater than $max");
        }

        if ($value < $min)
        {
            return $min;
        }

        if ($value > $max)
        {
            return $max;
        }

        return $value;
    }

    /**
     * Gets the number of digits of an integer.
     *
     * @param int $value
     *
     * @return int
     */
    public static function digitsOf(int $value): int
    {
        if ($value === 0)
        {
            return 1;
        }

        return (int) floor(log10(abs($value)) + 1);
    }

    /**
     * Calculates the factorial of `$n`.
     *
     * @param int $n The base value.
     *
     * @return int
     * @throws InvalidArgumentException If `$n` contains a negative value.
     */
    public static function factorial(int $n): int
    {
        if ($n < 0)
        {
            throw new InvalidArgumentException('Base value must be equal to or greater than zero');
        }

        $result = 1;
        for (; $n > 0; --$n)
        {
            $result *= $n;
        }

        return $result;
    }

    /**
     * Calculates the quotient and the remainder of two numbers.
     *
     * @param int|float $a The dividend.
     * @param int|float $b The divisor.
     * @param int|float|null $result The remainder.
     *
     * @return int|float The quotient.
     */
    public static function divRem(int|float $a, int|float $b, int|float|null &$result): int|float
    {
        $div = $a / $b;
        $result = abs($a - (round($div) * $b));
        return $div;
    }

    /**
     * Tries to convert a numeric string representation into an integer value.
     *
     * @param string $str The numeric string.
     * @param int|null $result Contains the result integer value if the conversion succeeded
     * or zero if the conversion failed.
     *
     * @return bool True if the conversion succeed, False otherwise.
     */
    public static function tryParseInt(string $str, ?int &$result): bool
    {
        $value = filter_var($str, FILTER_VALIDATE_INT);
        if ($value === false)
        {
            $result = 0;
            return false;
        }

        $result = $value;
        return true;
    }

    /**
     * Tries to convert a numeric string representation into a float value.
     *
     * @param string $str The numeric string.
     * @param float|null $result Contains the result value if the conversion succeeded
     * or zero if the conversion failed.
     *
     * @return bool True if the conversion succeed, False otherwise.
     */
    public static function tryParseFloat(string $str, ?float &$result): bool
    {
        $value = filter_var($str, FILTER_VALIDATE_FLOAT);
        if ($value === false)
        {
            $result = 0.0;
            return false;
        }

        $result = $value;
        return true;
    }

    /**
     * Throws an OverflowException if the add operation overflows PHP_INT_MAX.
     * If the add does not overflow, then the result is returned.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     * @throws OverflowException
     */
    public static function checkedAdd(int $a, int $b): int
    {
        if ((PHP_INT_MAX - $b) < $a)
        {
            throw new OverflowException('Addition operation overflows');
        }

        return $a + $b;
    }

    /**
     * Tries to add two integers and returns a boolean indicating whether an overflow occur or not.
     *
     * @param int $a
     * @param int $b
     * @param int|null The result of the sum or zero if an overflow occurs.
     *
     * @return bool True if the add succeeds, False if an overflow occurs.
     */
    public static function tryCheckedAdd(int $a, int $b, ?int &$result): bool
    {
        if ((PHP_INT_MAX - $b) < $a)
        {
            $result = 0;
            return false;
        }

        $result = $a + $b;
        return true;
    }

    /**
     * Throws an OverflowException if the subtract operation overflows PHP_INT_MIN.
     * If the subtract does not overflow, then the result is returned.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     * @throws OverflowException
     */
    public static function checkedSubtract(int $a, int $b): int
    {
        if ((PHP_INT_MIN + $b) > $a)
        {
            throw new OverflowException('Subtract operation overflows');
        }

        return $a - $b;
    }

    /**
     * Tries to subtract two integers and returns a boolean indicating whether an overflow occur or not.
     *
     * @param int $a
     * @param int $b
     * @param int|null $result The result of the subtract or zero if an overflow occurs.
     *
     * @return bool True if the add succeeds, False if an overflow occurs.
     */
    public static function tryCheckedSubtract(int $a, int $b, ?int &$result): bool
    {
        if ((PHP_INT_MIN + $b) > $a)
        {
            $result = 0;
            return false;
        }

        $result = $a - $b;
        return true;
    }

    /**
     * Static class.
     */
    private function __construct()
    {
    }
}
