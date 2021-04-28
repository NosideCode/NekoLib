<?php declare(strict_types=1);
namespace NekoLib\Collections;

use Iterator;
use function current;
use function key;
use function next;
use function reset;

/**
 * Iterates over the entries of a key/value list.
 */
final class KeyValuePairIterator implements Iterator
{
    /** @var KeyValuePair[] */
    private array $entries;

    /**
     * DictionaryIterator constructor.
     *
     * @param array $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * Gets the value of the current entry.
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->entries)->getValue();
    }

    /**
     * Moves to the next entry.
     */
    public function next(): void
    {
        next($this->entries);
    }

    /**
     * Gets the key of the current entry.
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return current($this->entries)->getKey();
    }

    /**
     * Determines whether the entry exists.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->entries) !== null;
    }

    /**
     * Rewinds the iterator.
     */
    public function rewind(): void
    {
        reset($this->entries);
    }
}
