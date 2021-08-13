<?php declare(strict_types=1);
namespace Neko\Collections;

use OutOfBoundsException;
use Traversable;
use function array_pop;
use function array_reverse;

/**
 * Represents a last-in-first-out (LIFO) collection.
 */
class Stack implements Collection
{
    private array $items = [];
    private int $size = 0;

    /**
     * Stack constructor.
     *
     * @param iterable|null $items A collection or array of initial values.
     */
    public function __construct(?iterable $items = null)
    {
        if ($items !== null)
        {
            foreach ($items as $value)
            {
                $this->push($value);
            }
        }
    }

    /**
     * Removes all values from the stack.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->size = 0;
    }

    /**
     * Determines whether the stack is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size === 0;
    }

    /**
     * Gets the number of items in the stack.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Determines whether the stack contains the given value.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        for ($i = 0; $i < $this->size; ++$i)
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
     * @param int $index The zero-based index in $array at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        for ($i = $this->size - 1; $i >= 0; --$i)
        {
            $destination[$index++] = $this->items[$i];
        }
    }

    /**
     * Gets the stack values as a one-dimensional array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_reverse($this->items);
    }

    /**
     * Gets an iterator for the stack.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new StackIterator($this->items, $this->size);
    }

    /**
     * Pushes a value at the top of the stack.
     *
     * @param mixed $value The value to push.
     */
    public function push(mixed $value): void
    {
        $this->items[] = $value;
        ++$this->size;
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

        --$this->size;
        return array_pop($this->items);
    }

    /**
     * Tries to remove and return the value at the top of the stack.
     *
     * @param mixed $result The value at the top of the stack or NULL if there is none.
     *
     * @return bool A boolean value that indicates whether the operation succeed or not.
     */
    public function tryPop(mixed &$result): bool
    {
        if ($this->isEmpty())
        {
            $result = null;
            return false;
        }

        --$this->size;
        $result = array_pop($this->items);
        return true;
    }

    /**
     * Gets the value at the top of the stack without removing it.
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

        return $this->items[$this->size - 1];
    }

    /**
     * Tries to get the value at the top of the stack.
     *
     * @param mixed $result The value at the top of the stack or NULL if there is none.
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

        $result = $this->items[$this->size - 1];
        return true;
    }
}
