<?php declare(strict_types=1);
namespace NekoLib\Collections;

/**
 * Represents an entry in a dictionary.
 * This class is intended to be used by the Dictionary class and should not be instantiated.
 */
final class DictionaryEntry
{
    public mixed $key;
    public mixed $value;
}
