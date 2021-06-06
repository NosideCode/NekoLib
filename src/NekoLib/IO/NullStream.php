<?php declare(strict_types=1);
namespace NekoLib\IO;

/**
 * A stream that points to the void of the universe.
 */
final class NullStream extends Stream
{
    public function canRead(): bool
    {
        return true;
    }

    public function canWrite(): bool
    {
        return true;
    }

    public function canSeek(): bool
    {
        return true;
    }

    public function endOfStream(): bool
    {
        return true;
    }

    public function getSize(): int
    {
        return 0;
    }

    public function setSize(int $size): void
    {
    }

    public function getPosition(): int
    {
        return 0;
    }

    public function setPosition(int $position): void
    {
    }

    public function seek(int $offset, int $whence): void
    {
    }

    public function read(int $length, ?int &$bytes_read): string
    {
        $bytes_read = 0;
        return '';
    }

    public function write(string $data, int $length = -1): int
    {
        return 0;
    }

    public function flush(): void
    {
    }

    public function close(): void
    {
    }

    public function copyTo(Stream $stream, int $buffer_size = 81920): void
    {
    }
}
