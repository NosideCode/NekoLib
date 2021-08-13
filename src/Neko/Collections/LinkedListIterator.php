<?php declare(strict_types=1);
namespace Neko\Collections;

use Iterator;
use Neko\InvalidOperationException;

/**
 * Iterates through the nodes of a linked list.
 */
final class LinkedListIterator implements Iterator
{
    private LinkedListNode $head;
    private ?LinkedListNode $node;

    /**
     * LinkedListIterator constructor.
     *
     * @param LinkedListNode $head The head node of the linked list.
     */
    public function __construct(LinkedListNode $head)
    {
        $this->head = $head;
        $this->node = $head;
    }

    public function current(): mixed
    {
        return $this->node->getValue();
    }

    public function next(): void
    {
        $this->node = $this->node->getNext();
    }

    /**
     * @throws InvalidOperationException
     */
    public function key(): void
    {
        throw new InvalidOperationException('Accessing the key is not valid for a linked list collection');
    }

    public function valid(): bool
    {
        return $this->node !== null;
    }

    public function rewind(): void
    {
        $this->node = $this->head;
    }
}
