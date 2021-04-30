<?php declare(strict_types=1);
namespace NekoLib\Collections;

use ArrayAccess;
use ArrayIterator;
use Iterator;
use OutOfBoundsException;
use function call_user_func;
use function count;
use function min;
use function sort;
use function usort;
use const SORT_REGULAR;

/**
 * Represents a list of values that can be accessed by index.
 */
class ArrayList implements ArrayAccess, Listable
{
    protected array $items = [];
    protected int $size = 0;

    /**
     * ArrayList constructor.
     *
     * @param Collection|array|null $collection A collection or array of initial values.
     */
    public function __construct(Collection|array $collection = null)
    {
        if ($collection !== null)
        {
            foreach ($collection as $value)
            {
                $this->add($value);
            }
        }
    }

    /**
     * Removes all values from the list.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->size = 0;
    }

    /**
     * Determines whether the list is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size === 0;
    }

    /**
     * Gets the number of items in the list.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Determines whether the list contains the given value.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        return $this->size > 0 && $this->indexOf($value) !== -1;
    }

    /**
     * Copies the values of the list to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in $array at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        for ($i = 0; $i < $this->size; ++$i)
        {
            $destination[$index++] = $this->items[$i];
        }
    }

    /**
     * Gets the list values as a one-dimensional array.
     *
     * @return array
     */
    public function toArray(): array
    {
        // We cannot use array_slice() as the order of the keys may have been lost,
        // so we need to copy each value manually.
        $result = [];
        for ($i = 0; $i < $this->size; ++$i)
        {
            $result[$i] = $this->items[$i];
        }

        return $result;
    }

    /**
     * Gets an iterator for the list.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Gets the index of the first occurrence of the value in the list.
     *
     * @param mixed $value The value to search.
     *
     * @return int The index of the value or -1 if the value does not exist in the list.
     */
    public function indexOf(mixed $value): int
    {
        for ($i = 0; $i < $this->size; ++$i)
        {
            if ($value === $this->items[$i])
            {
                return $i;
            }
        }

        return -1;
    }

    /**
     * Gets the index of the last occurrence of the value in the list.
     *
     * @param mixed $value The value to search.
     *
     * @return int The index of the value or -1 if the value does not exist in the list.
     */
    public function lastIndexOf(mixed $value): int
    {
        for ($i = $this->size - 1; $i >= 0; --$i)
        {
            if ($value === $this->items[$i])
            {
                return $i;
            }
        }

        return -1;
    }

    /**
     * Adds a value to the list.
     *
     * @param mixed $value
     */
    public function add(mixed $value): void
    {
        $this->items[$this->size++] = $value;
    }

    /**
     * Adds a collection or array of values to the list.
     *
     * @param Collection|array $collection The collection whose values should be added to the end of the list.
     */
    public function addRange(Collection|array $collection): void
    {
        $this->insertRange($this->size, $collection);
    }

    /**
     * Gets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to get.
     *
     * @return mixed
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function get(int $index): mixed
    {
        if ($index < 0 || $index >= $this->size)
        {
            throw new OutOfBoundsException('Index was out of range. Must be non-negative and less than the size of the list');
        }

        return $this->items[$index];
    }

    /**
     * Sets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to set.
     * @param mixed $value The value to set.
     *
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function set(int $index, mixed $value): void
    {
        if ($index < 0 || $index >= $this->size)
        {
            throw new OutOfBoundsException('Index must be within the bounds of the list');
        }

        $this->items[$index] = $value;
    }

    /**
     * Inserts a value into the list at the specified index.
     *
     * @param int $index The zero-based index at which the value should be inserted.
     * @param mixed $value The value to insert.
     *
     * @throws OutOfBoundsException If the index is less than zero or greater than the size of the list.
     */
    public function insert(int $index, mixed $value): void
    {
        if ($index < 0 || $index > $this->size)
        {
            throw new OutOfBoundsException('Index must be within the bounds of the list');
        }

        for ($i = $this->size; $i > $index; --$i)
        {
            $this->items[$i] = $this->items[$i - 1];
        }

        $this->items[$index] = $value;
        ++$this->size;
    }

    /**
     * Inserts a collection or array of values at the specified index in the list.
     *
     * @param int $index The zero-based index at which the values should be inserted.
     * @param Collection|array $collection The collection or array of values to insert.
     *
     * @throws OutOfBoundsException If the index is less than zero or greater than the size of the list.
     */
    public function insertRange(int $index, Collection|array $collection): void
    {
        if ($index < 0 || $index > $this->size)
        {
            throw new OutOfBoundsException('Index must be within the bounds of the list');
        }

        if ($collection instanceof Collection)
        {
            $collection = $collection->toArray();
        }

        $length = count($collection);
        $newLength = $this->size + $length;

        // Move values to make room
        for ($i = $newLength - 1; $i >= $index + $length; --$i)
        {
            $this->items[$i] = $this->items[$i - $length];
        }

        // Copy collection values
        for ($i = 0; $i < $length; ++$i)
        {
            $this->items[$index++] = $collection[$i];
        }

        $this->size = $newLength;
    }

    /**
     * Removes the first occurrence of the value in the list.
     *
     * @param mixed $value The value to remove.
     *
     * @return bool A boolean value that indicates whether the value was removed or not.
     */
    public function remove(mixed $value): bool
    {
        $index = $this->indexOf($value);
        if ($index >= 0)
        {
            $this->removeAt($index);
            return true;
        }

        return false;
    }

    /**
     * Removes the value at the specified index.
     *
     * @param int $index The zero based index of the value to remove.
     *
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function removeAt(int $index): void
    {
        if ($index < 0 || $index >= $this->size)
        {
            throw new OutOfBoundsException('Index was out of bounds. Must be non-negative and less than the size of the list');
        }

        --$this->size;
        for (; $index < $this->size; ++$index)
        {
            $this->items[$index] = $this->items[$index + 1];
        }

        $this->items[$this->size] = null;
    }

    /**
     * Removes a range of values from the list.
     *
     * @param int $index The zero-based index where the range of values to be removed begins.
     * @param int $count The number of values to remove. If the count is negative, nothing will be removed.
     *
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function removeRange(int $index, int $count): void
    {
        if ($index < 0 || $index >= $this->size)
        {
            throw new OutOfBoundsException('Index was out of bounds. Must be non-negative and less than the size of the list');
        }

        if ($count > 0)
        {
            // Make sure the number of values to remove is within the list boundaries.
            $count = min($count, $this->size);
            while ($index < $this->size)
            {
                $this->items[$index] = $this->items[$index + $count] ?? null;
                ++$index;
            }

            $this->size -= $count;
        }
    }

    /**
     * Reverses the order of the values in the list.
     */
    public function reverse(): void
    {
        $start = 0;
        $end = $this->size - 1;

        while ($start < $end)
        {
            $a = $this->items[$start];
            $b = $this->items[$end];

            // swap
            $this->items[$start] = $b;
            $this->items[$end] = $a;

            ++$start;
            --$end;
        }
    }

    /**
     * Extracts a slice of the list.
     *
     * @param int $index The zero-based index where the range of values begins.
     * @param int $count The number of values to copy. If the count is negative, nothing will be copied.
     *
     * @return ArrayList
     * @throws OutOfBoundsException If the index is less than zero or is equal to or greater than the size of the list.
     */
    public function slice(int $index, int $count): ArrayList
    {
        if ($index < 0 || $index >= $this->size)
        {
            throw new OutOfBoundsException('Index was out of bounds. Must be non-negative and less than the size of the list');
        }

        $count = min($count, $this->size);
        $list = new ArrayList();

        for ($i = 0; $i < $count; ++$i)
        {
            $list->add($this->items[$index++]);
        }

        return $list;
    }

    /**
     * Sorts the values in the list.
     *
     * @param int $sort_mode The sorting mode.
     *
     * @link https://www.php.net/manual/es/function.sort.php
     */
    public function sort(int $sort_mode = SORT_REGULAR): void
    {
        // We need an array that has no empty slots.
        $items = $this->toArray();
        sort($items, $sort_mode);
        $this->items = $items;
    }

    /**
     * Sort the values in the list using a user-defined comparison function.
     *
     * @param callable $comparator The comparison function must return an integer less than, equal to,
     * or greater than zero if the first argument is considered to be respectively less than, equal to,
     * or greater than the second.
     */
    public function usort(callable $comparator): void
    {
        $items = $this->toArray();
        usort($items, $comparator);
        $this->items = $items;
    }

    /**
     * Determines whether the values in the list match the condition.
     *
     * @param callable $match A function that must return a boolean value.
     *
     * @return bool
     */
    public function all(callable $match): bool
    {
        for ($i = 0; $i < $this->size; ++$i)
        {
            if (!call_user_func($match, $this->items[$i]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Determines whether at least one value in the list matches the condition.
     *
     * @param callable $match A function that must return a boolean value.
     *
     * @return bool
     */
    public function any(callable $match): bool
    {
        for ($i = 0; $i < $this->size; ++$i)
        {
            if (call_user_func($match, $this->items[$i]))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Filters the values of the list.
     *
     * @param callable $match A function that must return a boolean value.
     *
     * @return ArrayList
     */
    public function filter(callable $match): ArrayList
    {
        $list = new ArrayList();
        for ($i = 0; $i < $this->size; ++$i)
        {
            $value = $this->items[$i];
            if (call_user_func($match, $value))
            {
                $list->add($value);
            }
        }

        return $list;
    }

    /**
     * Determines whether the offset is within the list bounds.
     *
     * @param mixed $offset The zero-based offset.
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $offset >= 0 && $offset < $this->size;
    }

    /**
     * Gets the value at the specified offset.
     *
     * @param mixed $offset The zero-based offset of the value to get.
     *
     * @return mixed
     * @throws OutOfBoundsException If the offset is less than zero or is equal to or greater than the size of the list.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Sets the value at the specified offset.
     *
     * @param mixed $offset The zero-based offset of the value to set.
     * @param mixed $value The value to set.
     *
     * @throws OutOfBoundsException If the offset is less than zero or is equal to or greater than the size of the list.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Removes the value at the specified offset.
     *
     * @param mixed $offset The zero based offset of the value to remove.
     *
     * @throws OutOfBoundsException If the offset is less than zero or is equal to or greater than the size of the list.
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->removeAt($offset);
    }
}
