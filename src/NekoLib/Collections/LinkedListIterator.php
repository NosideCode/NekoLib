<?php declare(strict_types=1);
namespace NekoLib\Collections;

use Iterator;

/**
 * Iterates the nodes of a linked list.
 */
final class LinkedListIterator implements Iterator
{
    private ?LinkedListNode $node;

    /**
     * LinkedListIterator constructor.
     *
     * @param LinkedListNode $head The head node of the linked list.
     */
    public function __construct(LinkedListNode $head)
    {
        $this->node = $head;
    }

    /**
     * Returns the current node value.
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return $this->node->getValue();
    }

    /**
     * Moves to the next node.
     */
    public function next(): void
    {
        $this->node = $this->node->getNext();
    }

    /**
     * Returns the LinkedListNode as key.
     *
     * @return LinkedListNode
     */
    public function key(): LinkedListNode
    {
        return $this->node;
    }

    /**
     * Determines whether the current node is not null.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->node !== null;
    }

    /**
     * Does nothing.
     */
    public function rewind(): void
    {
        // It really does nothing.
    }
}
