<?php declare(strict_types=1);
namespace NekoLib\Collections;

use InvalidArgumentException;
use Iterator;

/**
 * Represents a doubly linked list.
 */
class LinkedList implements Collection
{
    private ?LinkedListNode $head = null;
    private ?LinkedListNode $tail = null;
    private int $size = 0;

    /**
     * LinkedList constructor.
     *
     * @param Collection|array|null $collection If not NULL, the linked list will copy the values in the collection.
     */
    public function __construct(Collection|array $collection = null)
    {
        if ($collection !== null)
        {
            foreach ($collection as $value)
            {
                $this->addLast($value);
            }
        }
    }

    /**
     * Removes all nodes from the linked list.
     */
    public function clear(): void
    {
        $this->head = null;
        $this->tail = null;
        $this->size = 0;
    }

    /**
     * Determines whether the linked list is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size === 0;
    }

    /**
     * Determines whether the value exists in the linked list.
     *
     * @param mixed $value The value to search.
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        $node = $this->head;
        while ($node !== null)
        {
            if ($node->getValue() === $value)
            {
                return true;
            }

            $node = $node->getNext();
        }

        return false;
    }

    /**
     * Copies the values of the linked list to an array.
     *
     * @param array $destination The destination array.
     * @param int $index The zero-based index in `$array` at which copy begins.
     */
    public function copyTo(array &$destination, int $index = 0): void
    {
        $node = $this->head;
        while ($node !== null)
        {
            $destination[$index++] = $node->getValue();
            $node = $node->getNext();
        }
    }

    /**
     * Returns the linked list values as a one-dimension array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = [];
        $node = $this->head;

        while ($node !== null)
        {
            $arr[] = $node->getValue();
            $node = $node->getNext();
        }

        return $arr;
    }

    /**
     * Returns an Iterator for the linked list.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->head);
    }

    /**
     * Returns the number of nodes in the linked list.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Returns the first node (head).
     *
     * @return LinkedListNode|null
     */
    public function getFirst(): ?LinkedListNode
    {
        return $this->head;
    }

    /**
     * Returns the last node (tail).
     *
     * @return LinkedListNode|null
     */
    public function getLast(): ?LinkedListNode
    {
        return $this->tail;
    }

    /**
     * Finds the first node that contains the value.
     *
     * @param mixed $value The value to search in the linked list.
     *
     * @return LinkedListNode|null The first node that contains the value, if found; otherwise null.
     */
    public function find(mixed $value): ?LinkedListNode
    {
        $node = $this->head;
        while ($node !== null)
        {
            if ($node->getValue() === $value)
            {
                break;
            }

            $node = $node->getNext();
        }

        return $node;
    }

    /**
     * Finds the last node that contains the value.
     *
     * @param mixed $value The value to search in the linked list.
     *
     * @return LinkedListNode|null The last node that contains the value, if found; otherwise null.
     */
    public function findLast(mixed $value): ?LinkedListNode
    {
        $node = $this->tail;
        while ($node !== null)
        {
            if ($node->getValue() === $value)
            {
                break;
            }

            $node = $node->getPrevious();
        }

        return $node;
    }

    /**
     * Adds a new value after an existing node.
     *
     * @param LinkedListNode $node The node after which insert the new value.
     * @param mixed $value The value to add.
     *
     * @return LinkedListNode The new node containing the value.
     * @throws InvalidArgumentException If the node does not belong to this linked list.
     */
    public function addAfter(LinkedListNode $node, mixed $value): LinkedListNode
    {
        if ($node->getList() !== $this)
        {
            throw new InvalidArgumentException('The node belongs to a different linked list');
        }

        $newNode = new LinkedListNode($this);
        $newNode->setValue($value);

        $newNode->setNext($node->getNext());
        $newNode->setPrevious($node);
        $node->setNext($newNode);
        $newNode->getNext()?->setPrevious($newNode);
        ++$this->size;

        if ($node === $this->tail)
        {
            $this->tail = $newNode;
        }

        if ($this->head === null)
        {
            $this->head = $newNode;
        }

        return $newNode;
    }

    /**
     * Adds a new value before an existing node.
     *
     * @param LinkedListNode $node The node before which insert the new value.
     * @param mixed $value The value to add.
     *
     * @return LinkedListNode The new node containing the value.
     * @throws InvalidArgumentException If the node does not belong to this linked list.
     */
    public function addBefore(LinkedListNode $node, mixed $value): LinkedListNode
    {
        if ($node->getList() !== $this)
        {
            throw new InvalidArgumentException('The node belongs to a different list');
        }

        $newNode = new LinkedListNode($this);
        $newNode->setValue($value);

        $newNode->setNext($node);
        $newNode->setPrevious($node->getPrevious());
        $node->setPrevious($newNode);
        $newNode->getPrevious()?->setNext($newNode);
        ++$this->size;

        if ($node === $this->head)
        {
            $this->head = $newNode;
        }

        if ($this->tail === null)
        {
            $this->tail = $newNode;
        }

        return $newNode;
    }

    /**
     * Adds a new value at the start of the linked list.
     *
     * @param mixed $value The value to add at the start of the linked list.
     *
     * @return LinkedListNode The new linked list containing the value.
     */
    public function addFirst(mixed $value): LinkedListNode
    {
        $node = new LinkedListNode($this);
        $node->setValue($value);

        $node->setNext($this->head);
        $this->head?->setPrevious($node);
        $this->head = $node;
        ++$this->size;

        if ($this->tail === null)
        {
            $this->tail = $node;
        }

        return $node;
    }

    /**
     * Adds a new value at the end of the linked list.
     *
     * @param mixed $value The value to add at the end of the linked list.
     *
     * @return LinkedListNode The new linked list containing the value.
     */
    public function addLast(mixed $value): LinkedListNode
    {
        $node = new LinkedListNode($this);
        $node->setValue($value);

        $node->setPrevious($this->tail);
        $this->tail?->setNext($node);
        $this->tail = $node;
        ++$this->size;

        if ($this->head === null)
        {
            $this->head = $node;
        }

        return $node;
    }

    /**
     * Remove the first occurrence of the value from the linked list.
     *
     * @param mixed $value
     */
    public function remove(mixed $value): void
    {
        $node = $this->find($value);
        $next = $node->getNext();
        $prev = $node->getPrevious();

        $prev->setNext($next);
        $next->setPrevious($prev);
        --$this->size;

        if ($this->head === $node)
        {
            $this->head = $next;
        }

        if ($this->tail === $node)
        {
            $this->tail = $prev;
        }
    }

    /**
     * Removes the first node (head) of the linked list.
     */
    public function removeFirst(): void
    {
        $next = $this->head?->getNext();
        $next?->setPrevious(null);
        $this->head = $next;
        --$this->size;
    }

    /**
     * Removes the last node (tail) of the linked list.
     */
    public function removeLast(): void
    {
        $prev = $this->tail?->getPrevious();
        $prev?->setNext(null);
        $this->tail = $prev;
        --$this->size;
    }
}
