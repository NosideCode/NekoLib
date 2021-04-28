<?php declare(strict_types=1);
namespace NekoLib;

/**
 * Defines methods for object comparison.
 */
interface Comparable
{
    /**
     * Compares the value with this instance.
     *
     * @param mixed $value The value to compare with this instance.
     *
     * @return int A value less than zero if this instance succeeds the given value.
     * A zero value if this instance equals the given value.
     * A value greater than zero if this instance precedes the given value.
     */
    public function compare(mixed $value): int;

    /**
     * Determines whether the value equals this instance.
     *
     * @param mixed $value The value to compare with this instance.
     *
     * @return bool
     */
    public function equals(mixed $value): bool;
}
