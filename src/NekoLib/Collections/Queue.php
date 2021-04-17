<?php declare(strict_types=1);
namespace NekoLib\Collections;

use ArrayIterator;
use Iterator;
use OutOfBoundsException;
use function array_shift;

/**
 * Represents a first-in-first-out (FIFO) collection.
 */
class Queue implements Collection
{
    private array $items = [];
    private int $length = 0;

    /**
     * Queue constructor.
     *
     * @param Collection|array|null $collection A collection or array of initial values.
     */
    public function __construct(Collection|array $collection = null)
    {
        if ($collection !== null)
        {
            foreach ($collection as $value)
            {
                $this->enqueue($value);
            }
        }
    }

    /**
     * Removes all values from the queue.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->length = 0;
    }

    /**
     * Determines whether the queue is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    /**
     * Gets the number of items in the queue.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->length;
    }

    /**
     * Determines whether the queue contains the given value.
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
     * Copies the values of the queue to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in $array at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        for ($i = 0; $i < $this->length; ++$i)
        {
            $destination[$index++] = $this->items[$i];
        }
    }

    /**
     * Gets the queue values as a one-dimensional array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Gets an iterator for the queue.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Adds a value at the end of the queue.
     *
     * @param mixed $value The value to add.
     */
    public function enqueue(mixed $value): void
    {
        $this->items[] = $value;
        ++$this->length;
    }

    /**
     * Removes and returns the value at the beginning of the queue.
     *
     * @return mixed
     * @throws OutOfBoundsException If the queue is empty.
     */
    public function dequeue(): mixed
    {
        if ($this->isEmpty())
        {
            throw new OutOfBoundsException('Queue is empty');
        }

        --$this->length;
        return array_shift($this->items);
    }

    /**
     * Tries to remove and return the value at the beginning of the queue.
     *
     * @param mixed $result The value at the beginning of the queue or NULL if there is none.
     *
     * @return bool A boolean value that indicates whether the operation succeed or not.
     */
    public function tryDequeue(mixed &$result): bool
    {
        if ($this->isEmpty())
        {
            $result = null;
            return false;
        }

        --$this->length;
        $result = array_shift($this->items);
        return true;
    }

    /**
     * Gets the value at the beginning of the queue without removing it.
     *
     * @return mixed
     * @throws OutOfBoundsException If the queue is empty.
     */
    public function peek(): mixed
    {
        if ($this->isEmpty())
        {
            throw new OutOfBoundsException('Queue is empty');
        }

        return $this->items[0];
    }

    /**
     * Tries to get the value at the beginning of the queue.
     *
     * @param mixed $result The value at the beginning of the queue or NULL if there is none.
     *
     * @return bool A boolean value that indicates whether the operation succeed or not.
     */
    public function tryPeek(mixed &$result): bool
    {
        if ($this->isEmpty())
        {
            $result = null;
            return false;
        }

        $result = $this->items[0];
        return true;
    }
}
