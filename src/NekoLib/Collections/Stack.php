<?php declare(strict_types=1);
namespace NekoLib\Collections;

use ArrayIterator;
use Iterator;
use OutOfBoundsException;

use function array_pop;
use function array_reverse;
use function count;

/**
 * Represents a last-in-first-out (LIFO) collection.
 */
class Stack implements Collection
{
    private array $items = [];
    private int $length = 0;

    /**
     * Stack constructor.
     *
     * @param Collection|array|null $collection If not NULL, the stack will copy the values in the collection.
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
     * Removes all values from the stack.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->length = 0;
    }

    /**
     * Checks if the stack is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    /**
     * Returns the number of values in the stack.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->length;
    }

    /**
     * Checks if the given value exists in the stack.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        for ($i = 0; $i < $this->length; ++$i)
        {
            if ($value === $this->items[$i])
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Copies the values of the stack to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in `$array` at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        for ($i = $this->length - 1; $i >= 0; --$i)
        {
            $destination[$index++] = $this->items[$i];
        }
    }

    /**
     * Returns the stack values in a one-dimension array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_reverse($this->items);
    }

    /**
     * Returns an iterator for the stack.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Pushes a new value at the top of the stack.
     *
     * @param mixed $value The value to push.
     */
    public function push(mixed $value): void
    {
        $this->items[] = $value;
        ++$this->length;
    }

    /**
     * Removes and returns the value at the top of the stack.
     *
     * @return mixed
     * @throws OutOfBoundsException If the stack is empty.
     */
    public function pop(): mixed
    {
        if ($this->isEmpty())
        {
            throw new OutOfBoundsException('Stack is empty');
        }

        --$this->length;
        return array_pop($this->items);
    }

    /**
     * Tries to get and remove the value at the top of the stack.
     *
     * @param mixed $result The value at the top of the stack or NULL if there is none.
     *
     * @return bool True if there is a value at the top of the stack or False if the stack is empty.
     */
    public function tryPop(mixed &$result): bool
    {
        if ($this->isEmpty())
        {
            $result = null;
            return false;
        }

        --$this->length;
        $result = array_pop($this->items);
        return true;
    }

    /**
     * Returns the value at the top of the stack without removing it.
     *
     * @return mixed
     * @throws OutOfBoundsException If the stack is empty.
     */
    public function peek(): mixed
    {
        if ($this->isEmpty())
        {
            throw new OutOfBoundsException('Stack is empty');
        }

        return $this->items[$this->length - 1];
    }

    /**
     * Tries to get the value at the top of the stack without removing it.
     *
     * @param mixed $result The value at the top of the stack or NULL if there is none.
     *
     * @return bool True if there is a value at the top of the stack or False if the stack is empty.
     */
    public function tryPeek(mixed &$result): bool
    {
        if ($this->isEmpty())
        {
            $result = null;
            return false;
        }

        $result = $this->items[$this->length - 1];
        return true;
    }
}
