<?php declare(strict_types=1);
namespace Neko\Collections;

/**
 * Represents a key/value pair.
 */
final class KeyValuePair
{
    private mixed $key;
    private mixed $value;

    /**
     * KeyValuePair constructor.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __construct(mixed $key, mixed $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Gets the key.
     *
     * @return mixed
     */
    public function getKey(): mixed
    {
        return $this->key;
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
