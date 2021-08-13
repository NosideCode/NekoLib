<?php declare(strict_types=1);
namespace Neko\Collections;

use Iterator;
use function current;
use function key;
use function next;
use function reset;

/**
 * Iterates through a key/value pair collection.
 */
final class KeyValuePairIterator implements Iterator
{
    /** @var KeyValuePair[] */
    private array $entries;

    /**
     * DictionaryIterator constructor.
     *
     * @param array $entries The key/value pairs.
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    public function current(): mixed
    {
        return current($this->entries)->getValue();
    }

    public function next(): void
    {
        next($this->entries);
    }

    public function key(): mixed
    {
        return current($this->entries)->getKey();
    }

    public function valid(): bool
    {
        return key($this->entries) !== null;
    }

    public function rewind(): void
    {
        reset($this->entries);
    }
}
