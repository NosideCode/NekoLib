<?php declare(strict_types=1);
namespace NekoLib\Collections;

use InvalidArgumentException;

/**
 * Represents a collection of key/value pairs.
 */
interface KeyValuePairCollection extends Collection
{
    /**
     * Adds a new key/value pair.
     *
     * @param mixed $key The key of the value.
     * @param mixed $value The value.
     *
     * @throws InvalidArgumentException If the key is null or a value with the same key already exists.
     */
    public function add(mixed $key, mixed $value): void;

    /**
     * Gets the value associated with the given key.
     *
     * @param mixed $key The key of the value to get.
     *
     * @return mixed
     * @throws InvalidArgumentException If the key is null.
     * @throws KeyNotFoundException If the key was not found.
     */
    public function get(mixed $key): mixed;

    /**
     * Sets a key/value pair.
     *
     * @param mixed $key The key of the value.
     * @param mixed $value The value.
     *
     * @throws InvalidArgumentException If the key is null.
     */
    public function set(mixed $key, mixed $value): void;

    /**
     * Removes the value associated with the given key.
     *
     * @param mixed $key The key of the value to remove.
     *
     * @throws InvalidArgumentException If the key is null.
     */
    public function remove(mixed $key): void;

    /**
     * Determines whether the dictionary contains the given key.
     *
     * @param mixed $key The key to search.
     *
     * @return bool
     * @throws InvalidArgumentException If the key is null.
     */
    public function containsKey(mixed $key): bool;

    /**
     * Gets the dictionary keys as a one-dimensional array.
     *
     * @return array
     */
    public function getKeys(): array;

    /**
     * Gets the dictionary values as a one-dimensional array.
     *
     * @return array
     */
    public function getValues(): array;
}
