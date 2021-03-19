<?php declare(strict_types=1);
namespace NekoLib\Collections;

use Countable;
use IteratorAggregate;

/**
 * Defines methods for collection manipulation.
 *
 * @package NekoLib\Collections
 */
interface Collection extends Countable, IteratorAggregate
{
    /**
     * Removes all values from the collection.
     */
    public function clear(): void;

    /**
     * Checks if the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Checks if the given value exists in the collection.
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
     * @param int $index The zero-based index in `$array` at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void;

    /**
     * Returns the collection as a one-dimension array.
     *
     * @return array
     */
    public function toArray(): array;
}
