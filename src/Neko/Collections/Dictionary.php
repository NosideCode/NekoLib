<?php declare(strict_types=1);
namespace Neko\Collections;

use ArrayAccess;
use InvalidArgumentException;
use Traversable;
use function array_key_exists;
use function gettype;
use function is_array;
use function is_object;
use function is_resource;
use function spl_object_hash;

/**
 * Represents a collection of keys and values.
 */
class Dictionary implements ArrayAccess, KeyValuePairCollection
{
    /** @var KeyValuePair[] */
    private array $entries = [];
    private int $size = 0;

    public function __construct(?KeyValuePairCollection $collection = null)
    {
        if ($collection !== null)
        {
            foreach ($collection as $key => $value)
            {
                $this->set($key, $value);
            }
        }
    }

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
        if ($value instanceof KeyValuePair)
        {
            $key = $this->createValidArrayKey($value->getKey());
            $entry = $this->entries[$key] ?? null;
            return $entry !== null && ($entry === $value || $entry->getValue() === $value->getValue());
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
        return array_key_exists($this->createValidArrayKey($key), $this->entries);
    }

    /**
     * Determines whether the dictionary contains the given value.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function containsValue(mixed $value): bool
    {
        foreach ($this->entries as $entry)
        {
            if ($entry->getValue() === $value)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Copies the key/value pairs of the dictionary to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in $array at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        foreach ($this->entries as $entry)
        {
            $destination[$index++] = $entry;
        }
    }

    /**
     * Gets the dictionary key/value pairs as a one-dimensional array.
     *
     * @return KeyValuePair[]
     */
    public function toArray(): array
    {
        $keyValuePairs = [];
        foreach ($this->entries as $entry)
        {
            $keyValuePairs[] = $entry;
        }

        return $keyValuePairs;
    }

    /**
     * Gets an iterator for the dictionary.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
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
        $arrayKey = $this->createValidArrayKey($key);
        if (array_key_exists($arrayKey, $this->entries))
        {
            throw new InvalidArgumentException('The key already exists');
        }

        $entry = new KeyValuePair($key, $value);
        $this->entries[$arrayKey] = $entry;
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
        $arrayKey = $this->createValidArrayKey($key);
        if (!array_key_exists($arrayKey, $this->entries))
        {
            throw new KeyNotFoundException($key);
        }

        return $this->entries[$arrayKey]->getValue();
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
        $arrayKey = $this->createValidArrayKey($key);
        $entry = $this->entries[$arrayKey] ?? null;

        if ($entry === null)
        {
            $entry = new KeyValuePair($key);
            $this->entries[$arrayKey] = $entry;
            ++$this->size;
        }

        $entry->setValue($value);
    }

    /**
     * Removes the value associated with the given key.
     *
     * @param mixed $key The key of the value to remove.
     *
     * @return bool A boolean value that indicates whether the value was removed or not.
     * @throws InvalidArgumentException If the key is null.
     */
    public function remove(mixed $key): bool
    {
        $key = $this->createValidArrayKey($key);
        if (array_key_exists($key, $this->entries))
        {
            unset($this->entries[$key]);
            --$this->size;
            return true;
        }

        return false;
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
            $keys[] = $entry->getKey();
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
            $values[] = $entry->getValue();
        }

        return $values;
    }

    /**
     * Flips all keys with their associated values.
     *
     * @return Dictionary
     */
    public function flip(): Dictionary
    {
        $flipped = new Dictionary();
        foreach ($this->entries as $entry)
        {
            $key = $entry->getValue();
            $val = $entry->getKey();
            $flipped->set($key, $val);
        }

        return $flipped;
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
        return $this->containsKey($offset);
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
     * Creates an array-valid key.
     *
     * @param mixed $value The value to use to create the key.
     *
     * @return string The array key.
     * @throws InvalidArgumentException If the key is either null, an array, or a resource.
     */
    private function createValidArrayKey(mixed $value): string
    {
        if ($value === null)
        {
            throw new InvalidArgumentException('The key cannot be null');
        }

        if (is_array($value))
        {
            throw new InvalidArgumentException('The key cannot be an array');
        }

        if (is_resource($value))
        {
            throw new InvalidArgumentException('The key cannot be a resource');
        }

        if (is_object($value))
        {
            return 'object:' . spl_object_hash($value);
        }

        // Other types: int, float, string, bool
        $type = gettype($value);
        return "$type:$value";
    }
}
