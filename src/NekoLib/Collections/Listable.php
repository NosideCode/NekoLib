<?php declare(strict_types=1);
namespace NekoLib\Collections;

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
     * @throws OutOfBoundsException If index is less than zero or is equal to or greater than the size of the collection.
     */
    public function get(int $index): mixed;

    /**
     * Sets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to set.
     * @param mixed $value The value to set.
     *
     * @throws OutOfBoundsException If index is less than zero or is equal to or greater than the size of the collection.
     */
    public function set(int $index, mixed $value): void;

    /**
     * Returns the index of the first occurrence of the value in the list.
     *
     * @param mixed $value The value to search.
     *
     * @return int The index of the value or -1 if the value does not exists in the list.
     */
    public function indexOf(mixed $value): int;

    /**
     * Inserts a value to the list at the specified index.
     *
     * @param int $index The zero-based index at which the value should be inserted.
     * @param mixed $value The value to insert.
     *
     * @throws OutOfBoundsException If index is less than zero or is equal to or greater than the size of the collection.
     */
    public function insert(int $index, mixed $value): void;

    /**
     * Removes the first occurrence of the value in the list.
     *
     * @param mixed $value The value to remove.
     *
     * @throws OutOfBoundsException If index is less than zero or is equal to or greater than the size of the collection.
     */
    public function remove(mixed $value): void;

    /**
     * Removes the list value at the specified index.
     *
     * @param int $index The zero-based index of the value to remove.
     */
    public function removeAt(int $index): void;
}
