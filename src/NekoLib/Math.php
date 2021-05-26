<?php declare(strict_types=1);
namespace NekoLib;

use InvalidArgumentException;
use OverflowException;
use UnderflowException;
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
 * Contains methods for performing numeric operations.
 */
final class Math
{
    /**
     * Clamps the value to the inclusive range of min and max.
     *
     * @param int|float $value The value to clamp.
     * @param int|float $min The minimum value.
     * @param int|float $max The maximum value.
     *
     * @return int|float
     * @throws InvalidArgumentException If the minimum value is greater than the maximum value.
     */
    public static function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        if ($min > $max)
        {
            throw new InvalidArgumentException("'$min' cannot be greater than $max");
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
     * Gets the digit count of a number.
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
     * Gets the factorial of the given value.
     *
     * @param int $n The base value.
     *
     * @return int
     * @throws InvalidArgumentException If $n has a negative value.
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
     * Calculates the quotient and the remainder of two numbers, returning the quotient.
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
     * Tries to convert a numeric string to an integer.
     *
     * @param string $str The numeric string.
     * @param int|null $result The conversion result. If the conversion fails, the result value is zero.
     *
     * @return bool A boolean value that indicates whether the conversion was successful or not.
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
     * Tries to convert a numeric string to a float value.
     *
     * @param string $str The numeric string.
     * @param float|null $result The conversion result. If the conversion fails, the result value is zero.
     *
     * @return bool A boolean value that indicates whether the conversion was successful or not.
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
     * Adds two integers. If the result causes an overflow, throws an OverflowException.
     *
     * @param int $a
     * @param int $b
     *
     * @return int The sum of $a and $b.
     * @throws OverflowException
     */
    public static function checkedAdd(int $a, int $b): int
    {
        if ((PHP_INT_MAX - $b) < $a)
        {
            throw new OverflowException('The arithmetic operation overflows');
        }

        return $a + $b;
    }

    /**
     * Tries to add two integers.
     *
     * @param int $a
     * @param int $b
     * @param int|null $result The sum of $a and $b. If an overflow occurs, the result value is zero.
     *
     * @return bool A Boolean value that indicates whether or not an overflow occurred.
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
     * Subtracts two integers. If the result produces an overflow, throw an UnderflowException.
     *
     * @param int $a
     * @param int $b
     *
     * @return int The difference of $a and $b.
     * @throws UnderflowException
     */
    public static function checkedSubtract(int $a, int $b): int
    {
        if ((PHP_INT_MIN + $b) > $a)
        {
            throw new UnderflowException('Arithmetic operation underflow');
        }

        return $a - $b;
    }

    /**
     * Tries to subtract two integers.
     *
     * @param int $a
     * @param int $b
     * @param int|null $result The difference of $a and $b. If an overflow occurs, the result value is zero.
     *
     * @return bool A Boolean value that indicates whether or not an overflow occurred.
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
