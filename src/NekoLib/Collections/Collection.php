<?php declare(strict_types=1);
namespace NekoLib\Collections;

use Countable;
use IteratorAggregate;

/**
 * Defines methods for collection manipulation.
 */
interface Collection extends Countable, IteratorAggregate
{
    /**
     * Removes all values from the collection.
     */
    public function clear(): void;

    /**
     * Determines whether the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Determines whether the collection contains the given value.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool;

    /**
     * Copies the values of the collection to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in $array at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void;

    /**
     * Gets the collection values as a one-dimensional array.
     *
     * @return array
     */
    public function toArray(): array;
}
