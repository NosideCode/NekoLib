<?php declare(strict_types=1);
namespace NekoLib\Collections;

use OutOfBoundsException;

/**
 * Defines a collection of values that can be individually accessed by an index.
 */
interface Listable extends Collection
{
    /**
     * Adds a value to the list.
     *
     * @param mixed $value
     */
    public function add(mixed $value): void;

    /**
     * Gets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to get.
     *
     * @return mixed
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function get(int $index): mixed;

    /**
     * Sets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to set.
     * @param mixed $value The value to set.
     *
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function set(int $index, mixed $value): void;

    /**
     * Gets the index of the first occurrence of the value in the list.
     *
     * @param mixed $value The value to search.
     *
     * @return int The index of the value or -1 if the value does not exist in the list.
     */
    public function indexOf(mixed $value): int;

    /**
     * Inserts a value into the list at the specified index.
     *
     * @param int $index The zero-based index at which the value should be inserted.
     * @param mixed $value The value to insert.
     *
     * @throws OutOfBoundsException If the index is less than zero or greater than the size of the list.
     */
    public function insert(int $index, mixed $value): void;

    /**
     * Removes the first occurrence of the value in the list.
     *
     * @param mixed $value The value to remove.
     */
    public function remove(mixed $value): void;

    /**
     * Removes the value at the specified index.
     *
     * @param int $index The zero based index of the value to remove.
     *
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function removeAt(int $index): void;
}
