<?php declare(strict_types=1);
namespace NekoLib\Collections;

use ArrayAccess;
use InvalidArgumentException;
use Iterator;
use function array_key_exists;
use function is_object;
use function spl_object_hash;

/**
 * Represents a collection of keys and values.
 */
class Dictionary implements ArrayAccess, KeyValuePairCollection
{
    /** @var DictionaryEntry[] */
    private array $entries = [];
    private int $size = 0;

    /**
     * Removes all values from the collection.
     */
    public function clear(): void
    {
        $this->entries = [];
        $this->size = 0;
    }

    /**
     * Determines whether the dictionary is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size === 0;
    }

    /**
     * Determines whether the dictionary contains the given value.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        foreach ($this->entries as $entry)
        {
            if ($entry->value === $value)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines whether the dictionary contains the given key.
     *
     * @param mixed $key The key to search.
     *
     * @return bool
     * @throws InvalidArgumentException If the key is null.
     */
    public function containsKey(mixed $key): bool
    {
        $this->filterObjectKey($key);
        return array_key_exists($key, $this->entries);
    }

    /**
     * Copies the keys and values of the dictionary to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in $array at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        foreach ($this->entries as $entry)
        {
            $destination[$index++] = [$entry->key, $entry->value];
        }
    }

    /**
     * Gets the dictionary keys and values as a two-dimensional array of the form [key, value].
     * ```
     * $entries = $dictionary->toArray();
     * $first   = $entries[0];
     * $key     = $first[0];
     * $value   = $first[1];
     * ```
     * @return array
     */
    public function toArray(): array
    {
        $keyValuePairs = [];
        foreach ($this->entries as $entry)
        {
            $keyValuePairs[] = [$entry->key, $entry->value];
        }

        return $keyValuePairs;
    }

    /**
     * Gets an iterator for the dictionary.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new KeyValuePairIterator($this->entries);
    }

    /**
     * Gets the number of entries in the dictionary.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Adds a new key/value pair.
     *
     * @param mixed $key The key of the value.
     * @param mixed $value The value.
     *
     * @throws InvalidArgumentException If the key is Null or a value with the same key already exists.
     */
    public function add(mixed $key, mixed $value): void
    {
        $this->filterObjectKey($key, $rawKey);
        if (array_key_exists($key, $this->entries))
        {
            throw new InvalidArgumentException('The key already exists');
        }

        $entry = new DictionaryEntry();
        $entry->key = $rawKey;
        $entry->value = $value;
        $this->entries[$key] = $entry;
        ++$this->size;
    }

    /**
     * Gets the value associated with the given key.
     *
     * @param mixed $key The key of the value to get.
     *
     * @return mixed
     * @throws InvalidArgumentException If the key is null.
     * @throws KeyNotFoundException If the key was not found.
     */
    public function get(mixed $key): mixed
    {
        $this->filterObjectKey($key, $rawKey);
        if (!array_key_exists($key, $this->entries))
        {
            throw new KeyNotFoundException($rawKey);
        }

        return $this->entries[$key]->value;
    }

    /**
     * Sets a key/value pair.
     *
     * @param mixed $key The key of the value.
     * @param mixed $value The value.
     *
     * @throws InvalidArgumentException If the key is null.
     */
    public function set(mixed $key, mixed $value): void
    {
        $this->filterObjectKey($key, $rawKey);
        $entry = $this->entries[$key] ?? null;

        if ($entry === null)
        {
            $entry = new DictionaryEntry();
            $entry->key = $rawKey;
            $this->entries[$key] = $entry;
            ++$this->size;
        }

        $entry->value = $value;
    }

    /**
     * Removes the value associated with the given key.
     *
     * @param mixed $key The key of the value to remove.
     *
     * @throws InvalidArgumentException If the key is null.
     */
    public function remove(mixed $key): void
    {
        $this->filterObjectKey($key);
        unset($this->entries[$key]);
    }

    /**
     * Gets the dictionary keys as a one-dimensional array.
     *
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];
        foreach ($this->entries as $entry)
        {
            $keys[] = $entry->key;
        }

        return $keys;
    }

    /**
     * Gets the dictionary values as a one-dimensional array.
     *
     * @return array
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->entries as $entry)
        {
            $values[] = $entry->value;
        }

        return $values;
    }

    /**
     * Determines whether the key offset exists in the dictionary.
     *
     * @param mixed $offset The key.
     *
     * @return bool
     * @throws InvalidArgumentException If the key offset is null.
     */
    public function offsetExists(mixed $offset): bool
    {
        $this->filterObjectKey($offset);
        return array_key_exists($offset, $this->entries);
    }

    /**
     * Gets the value associated with the given key offset.
     *
     * @param mixed $offset The key offset to search.
     *
     * @return mixed
     * @throws KeyNotFoundException
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Sets the key/value pair.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Removes the value at the specified offset.
     *
     * @param mixed $offset The key associated with the value to remove.
     *
     * @throws InvalidArgumentException If the key is null.
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    /**
     * Filters the given key hashing the object if needed.
     *
     * @param mixed $key The key to filter.
     * @param mixed|null $rawKey The raw key.
     *
     * @throws InvalidArgumentException If the key is null.
     */
    private function filterObjectKey(mixed &$key, mixed &$rawKey = null): void
    {
        if ($key === null)
        {
            throw new InvalidArgumentException('The key cannot be null');
        }

        $rawKey = $key;
        if (is_object($key))
        {
            $key = spl_object_hash($key);
        }
    }
}
