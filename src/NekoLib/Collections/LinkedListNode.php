<?php declare(strict_types=1);
namespace NekoLib\Collections;

/**
 * Represents a node in a LinkedList.
 */
final class LinkedListNode
{
    private LinkedList $list;
    private ?LinkedListNode $next = null;
    private ?LinkedListNode $prev = null;
    private mixed $value;

    /**
     * LinkedListNode constructor.
     *
     * @param LinkedList $list The linked list that the node belongs to.
     */
    public function __construct(LinkedList $list)
    {
        $this->list = $list;
    }

    /**
     * Gets the linked list that the node belongs to.
     *
     * @return LinkedList
     */
    public function getList(): LinkedList
    {
        return $this->list;
    }

    /**
     * Gets the next node.
     *
     * @return LinkedListNode|null
     */
    public function getNext(): ?LinkedListNode
    {
        return $this->next;
    }

    /**
     * Sets the next node.
     *
     * @param LinkedListNode|null $next
     */
    public function setNext(?LinkedListNode $next): void
    {
        $this->next = $next;
    }

    /**
     * Gets the previous node.
     *
     * @return LinkedListNode|null
     */
    public function getPrevious(): ?LinkedListNode
    {
        return $this->prev;
    }

    /**
     * Sets the previous node.
     *
     * @param LinkedListNode|null $previous
     */
    public function setPrevious(?LinkedListNode $previous): void
    {
        $this->prev = $previous;
    }

    /**
     * Gets the value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Sets the value.
     *
     * @param mixed $value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
