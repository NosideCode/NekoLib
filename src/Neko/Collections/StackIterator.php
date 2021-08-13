<?php declare(strict_types=1);
namespace Neko\Collections;

use Iterator;
use Neko\InvalidOperationException;

/**
 * Iterates through the values of a stack.
 */
final class StackIterator implements Iterator
{
    private array $items;
    private int $size;
    private int $cursor;

    /**
     * StackIterator constructor.
     *
     * @param array $items The array to be iterated on.
     * @param int $size The number of values that the array contains.
     */
    public function __construct(array &$items, int $size)
    {
        $this->items = &$items;
        $this->size = $size;
        $this->cursor = $size - 1;
    }

    public function current(): mixed
    {
        return $this->items[$this->cursor];
    }

    public function next(): void
    {
        --$this->cursor;
    }

    /**
     * @throws InvalidOperationException
     */
    public function key(): void
    {
        throw new InvalidOperationException('Accessing the key is not valid for a stack collection');
    }

    public function valid(): bool
    {
        return $this->cursor >= 0;
    }

    public function rewind(): void
    {
        $this->cursor = $this->size - 1;
    }
}
