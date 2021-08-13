<?php declare(strict_types=1);
namespace Neko\Collections;

use InvalidArgumentException;
use Traversable;

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
     * @param iterable|null $items A collection or array of initial values.
     */
    public function __construct(?iterable $items = null)
    {
        if ($items !== null)
        {
            foreach ($items as $value)
            {
                $this->addLast($value);
            }
        }
    }

    /**
     * Removes all values from the linked list.
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
     * Determines whether the linked list contains the given value.
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
     * @param int $index The zero-based index in $array at which copy begins.
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
     * Gets the linked list values as a one-dimensional array.
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
     * Gets an Iterator for the linked list.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new LinkedListIterator($this->head);
    }

    /**
     * Gets the number of nodes in the linked list.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Gets the first node.
     *
     * @return LinkedListNode|null
     */
    public function getFirst(): ?LinkedListNode
    {
        return $this->head;
    }

    /**
     * Gets the last node.
     *
     * @return LinkedListNode|null
     */
    public function getLast(): ?LinkedListNode
    {
        return $this->tail;
    }

    /**
     * Finds the node that contains the first occurrence of the value.
     *
     * @param mixed $value The value to search.
     *
     * @return LinkedListNode|null The node that contains the value or NULL if the value was not found.
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
     * Finds the node that contains the last occurrence of the value.
     *
     * @param mixed $value The value to search.
     *
     * @return LinkedListNode|null The node that contains the value or NULL if the value was not found.
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
     * Adds a value after a node.
     *
     * @param LinkedListNode $node The reference node after which the value should be inserted.
     * @param mixed $value The value to add.
     *
     * @return LinkedListNode A new node containing the added value.
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
        $this->size++;

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
     * Adds a value before a node.
     *
     * @param LinkedListNode $node The reference node before which the value should be inserted.
     * @param mixed $value The value to add.
     *
     * @return LinkedListNode A new node containing the added value.
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
        $this->size++;

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
     * Adds a value at the beginning of the linked list.
     *
     * @param mixed $value The value to add.
     *
     * @return LinkedListNode A new node containing the added value.
     */
    public function addFirst(mixed $value): LinkedListNode
    {
        $node = new LinkedListNode($this);
        $node->setValue($value);

        $node->setNext($this->head);
        $this->head?->setPrevious($node);
        $this->head = $node;
        $this->size++;

        if ($this->tail === null)
        {
            $this->tail = $node;
        }

        return $node;
    }

    /**
     * Adds a value at the end of the linked list.
     *
     * @param mixed $value The value to add.
     *
     * @return LinkedListNode A new node containing the added value.
     */
    public function addLast(mixed $value): LinkedListNode
    {
        $node = new LinkedListNode($this);
        $node->setValue($value);

        $node->setPrevious($this->tail);
        $this->tail?->setNext($node);
        $this->tail = $node;
        $this->size++;

        if ($this->head === null)
        {
            $this->head = $node;
        }

        return $node;
    }

    /**
     * Remove the first occurrence of the value from the linked list.
     *
     * @param mixed $value The value to remove.
     *
     * @return bool A boolean value that indicates whether the value was removed or not.
     */
    public function remove(mixed $value): bool
    {
        $node = $this->find($value);
        if ($node !== null)
        {
            $this->removeNode($node);
            return true;
        }

        return false;
    }

    /**
     * Removes a node from the linked list.
     *
     * @param LinkedListNode $node The node to remove.
     *
     * @throws InvalidArgumentException If the node does not belong to this linked list.
     */
    public function removeNode(LinkedListNode $node): void
    {
        if ($node->getList() !== $this)
        {
            throw new InvalidArgumentException('The node belongs to a different list');
        }

        $next = $node->getNext();
        $prev = $node->getPrevious();

        $prev->setNext($next);
        $next->setPrevious($prev);
        $this->size--;

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
     * Removes the first node from the linked list.
     */
    public function removeFirst(): void
    {
        $next = $this->head?->getNext();
        $next?->setPrevious(null);
        $this->head = $next;
        $this->size--;
    }

    /**
     * Removes the last node from the linked list.
     */
    public function removeLast(): void
    {
        $prev = $this->tail?->getPrevious();
        $prev?->setNext(null);
        $this->tail = $prev;
        $this->size--;
    }
}
