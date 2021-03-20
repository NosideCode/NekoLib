<?php declare(strict_types=1);
namespace NekoLib\Collections;

use ArrayIterator;
use Iterator;
use OutOfBoundsException;

use function call_user_func;
use function count;
use function min;
use function sort;
use function usort;
use const SORT_REGULAR;

class ArrayList implements Listable
{
    private array $items = [];
    private int $length = 0;

    /**
     * ArrayList constructor.
     *
     * @param Collection|array|null $collection If not NULL, the list will copy the values in the collection.
     */
    public function __construct(Collection|array $collection = null)
    {
        if ($collection !== null)
        {
            if ($collection instanceof Collection)
            {
                $collection = $collection->toArray();
            }

            $this->items = $collection;
            $this->length = count($collection);
        }
    }

    /**
     * Removes all values from the list.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->length = 0;
    }

    /**
     * Checks if the list is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    /**
     * Returns the number of values in the list.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->length;
    }

    /**
     * Checks if the given value exists in the list.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        return $this->length > 0 && $this->indexOf($value) !== -1;
    }

    /**
     * Copies the values of the list to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in `$array` at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        for ($i = 0; $i < $this->length; ++$i)
        {
            $destination[$index++] = $this->items[$i];
        }
    }

    /**
     * Returns the list as a one-dimension array.
     *
     * @return array
     */
    public function toArray(): array
    {
        // The order of the keys may be lost, (glaring at you, insertRange())
        // so we need to manually copy each value in a new array.
        $result = [];
        for ($i = 0; $i < $this->length; ++$i)
        {
            $result[$i] = $this->items[$i];
        }

        return $result;
    }

    /**
     * Returns an iterator for the list.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Returns the index of the first occurrence of the value in the list.
     *
     * @param mixed $value The value to search.
     *
     * @return int The index of the value or -1 if the value does not exists in the list.
     */
    public function indexOf(mixed $value): int
    {
        for ($i = 0; $i < $this->length; ++$i)
        {
            if ($value === $this->items[$i])
            {
                return $i;
            }
        }

        return -1;
    }

    /**
     * Returns the index of the last occurrence of the value in the list.
     * Returns the zero-based index of the last occurrence of a value in the List<T> or in a portion of it.
     *
     * @param mixed $value
     *
     * @return int The index of the value or -1 if the value does not exists in the list.
     */
    public function lastIndexOf(mixed $value): int
    {
        for ($i = $this->length - 1; $i >= 0; --$i)
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
        $this->items[$this->length++] = $value;
    }

    /**
     * Adds the values of the collection at the end of the list.
     *
     * @param Collection|array $collection The collection whose values should be added to the end of the list.
     */
    public function addRange(Collection|array $collection): void
    {
        $this->insertRange($this->length, $collection);
    }

    /**
     * Gets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to get.
     *
     * @return mixed
     * @throws OutOfBoundsException If `$index` is less than zero or is equal to or greater than the size of the collection.
     */
    public function get(int $index): mixed
    {
        if ($index < 0 || $index >= $this->length)
        {
            throw new OutOfBoundsException('Index was out of range. Must be non-negative and less than the size of the collection');
        }

        return $this->items[$index];
    }

    /**
     * Sets the value at the specified index.
     *
     * @param int $index The zero-based index of the value to set.
     * @param mixed $value The value to set.
     *
     * @throws OutOfBoundsException If `$index` is less than zero or is equal to or greater than the size of the collection.
     */
    public function set(int $index, mixed $value): void
    {
        if ($index < 0 || $index >= $this->length)
        {
            throw new OutOfBoundsException('Index must be within the bounds of the list');
        }

        $this->items[$index] = $value;
    }

    /**
     * Inserts a value to the list at the specified index.
     *
     * @param int $index The zero-based index at which the value should be inserted.
     * @param mixed $value The value to insert.
     *
     * @throws OutOfBoundsException If `$index` is less than zero or greater than the size of the collection.
     */
    public function insert(int $index, mixed $value): void
    {
        if ($index < 0 || $index > $this->length)
        {
            throw new OutOfBoundsException('Index must be within the bounds of the list');
        }

        for ($i = $this->length; $i > $index; --$i)
        {
            $this->items[$i] = $this->items[$i - 1];
        }

        $this->items[$index] = $value;
        ++$this->length;
    }

    /**
     * Inserts the values of the collection into the list at the specified index.
     *
     * @param int $index The zero-based index at which the new values should be inserted.
     * @param Collection|array $collection
     *
     * @throws OutOfBoundsException If `$index` is less than zero or greater than the size of the collection.
     */
    public function insertRange(int $index, Collection|array $collection): void
    {
        if ($index < 0 || $index > $this->length)
        {
            throw new OutOfBoundsException('Index must be within the bounds of the list');
        }

        if ($collection instanceof Collection)
        {
            $collection = $collection->toArray();
        }

        $length = count($collection);
        $newLength = $this->length + $length;

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

        $this->length = $newLength;
    }

    /**
     * Removes the first occurrence of the value in the list.
     *
     * @param mixed $value The value to remove.
     */
    public function remove(mixed $value): void
    {
        $index = $this->indexOf($value);
        if ($index >= 0)
        {
            $this->removeAt($index);
        }
    }

    /**
     * Removes the list value at the specified index.
     *
     * @param int $index The zero-based index of the value to remove.
     *
     * @throws OutOfBoundsException If `$index` is less than zero or is equal to or greater than the size of the collection.
     */
    public function removeAt(int $index): void
    {
        if ($index < 0 || $index >= $this->length)
        {
            throw new OutOfBoundsException('Index was out of bounds. Must be non-negative and less than the size of the list');
        }

        --$this->length;
        for (; $index < $this->length; ++$index)
        {
            $this->items[$index] = $this->items[$index + 1];
        }

        $this->items[$this->length] = null;
    }

    /**
     * Removes a range of values from the list.
     *
     * @param int $index The zero-based starting index of the range of values to remove.
     * @param int $count The number of values to remove. If the count is negative, nothing will be removed from the list.
     *
     * @throws OutOfBoundsException If `$index` is less than zero or is equal to or greater than the size of the collection.
     */
    public function removeRange(int $index, int $count): void
    {
        if ($index < 0 || $index >= $this->length)
        {
            throw new OutOfBoundsException('Index was out of bounds. Must be non-negative and less than the size of the list');
        }

        if ($count > 0)
        {
            // Make sure the number of values to remove is within the list boundaries.
            $count = min($count, $this->length);
            while ($index < $this->length)
            {
                $this->items[$index] = $this->items[$index + $count] ?? null;
                ++$index;
            }

            $this->length -= $count;
        }
    }

    /**
     * Reverses the order of the values in the list.
     */
    public function reverse(): void
    {
        $start = 0;
        $end = $this->length - 1;

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
     * Extracts a slice of the list at the specified index.
     *
     * @param int $index The zero-based starting index where the slicing begins.
     * @param int $count The number of values to take.
     *
     * @return ArrayList A new list containing the sliced values. If `$count` is zero or a negative
     * value, an empty list is returned.
     * @throws OutOfBoundsException If `$index` is less than zero or is equal to or greater than the size of the collection.
     */
    public function slice(int $index, int $count): ArrayList
    {
        if ($index < 0 || $index >= $this->length)
        {
            throw new OutOfBoundsException('Index was out of bounds. Must be non-negative and less than the size of the list');
        }

        $count = min($count, $this->length);
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
     * Sorts the values in the list using a custom comparison function.
     *
     * @param callable $comparator The comparison function that must return an integer less than, equal to,
     * or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.
     */
    public function usort(callable $comparator): void
    {
        $items = $this->toArray();
        usort($items, $comparator);
        $this->items = $items;
    }

    /**
     * Checks that all the values in the list matches the condition.
     *
     * @param callable $match A callable that returns a boolean value.
     *
     * @return bool
     */
    public function all(callable $match): bool
    {
        for ($i = 0; $i < $this->length; ++$i)
        {
            if (!call_user_func($match, $this->items[$i]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks that at least one of the values in the list matches the condition.
     *
     * @param callable $match A callable that returns a boolean value.
     *
     * @return bool
     */
    public function any(callable $match): bool
    {
        for ($i = 0; $i < $this->length; ++$i)
        {
            if (call_user_func($match, $this->items[$i]))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Filters the values of the list that matches the condition.
     *
     * @param callable $match A callable that returns a boolean value.
     *
     * @return ArrayList A new list containing the filtered values.
     */
    public function filter(callable $match): ArrayList
    {
        $list = new ArrayList();
        for ($i = 0; $i < $this->length; ++$i)
        {
            $value = $this->items[$i];
            if (call_user_func($match, $value))
            {
                $list->add($value);
            }
        }

        return $list;
    }
}
