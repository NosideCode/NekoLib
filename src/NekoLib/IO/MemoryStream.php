<?php declare(strict_types=1);
namespace NekoLib\IO;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function ftruncate;
use function fwrite;
use function strlen;
use const SEEK_SET;

/**
 * Creates a Stream in memory.
 */
class MemoryStream extends Stream
{
    protected mixed $memory;

    /**
     * MemoryStream constructor.
     *
     * @throws IOException If the stream could not be open.
     */
    public function __construct()
    {
        $this->memory = fopen('php://memory', 'rb+');
        if ($this->memory === false)
        {
            throw new IOException('Failed to open the memory stream');
        }
    }

    /**
     * Determines whether the stream can be read.
     *
     * @return bool
     */
    public function canRead(): bool
    {
        return true;
    }

    /**
     * Determines whether the stream can be written.
     *
     * @return bool
     */
    public function canWrite(): bool
    {
        return true;
    }

    /**
     * Determines whether the stream has reached the end.
     *
     * @return bool
     */
    public function endOfStream(): bool
    {
        return feof($this->memory);
    }

    /**
     * Returns the stream size.
     *
     * @return int
     * @throws IOException If fstat failed.
     */
    public function getSize(): int
    {
        $stat = fstat($this->memory);
        if ($stat === false)
        {
            throw new IOException('fstat failed');
        }

        return $stat['size'];
    }

    /**
     * Sets the size of the stream.
     *
     * @param int $size The new size value. If the value is less than the current
     * stream length, the stream is truncated.
     *
     * @throws IOException If ftruncate fails.
     */
    public function setSize(int $size): void
    {
        if (!ftruncate($this->memory, $size))
        {
            throw new IOException('ftruncate failed');
        }
    }

    /**
     * Returns the current position in the stream.
     *
     * @return int
     * @throws IOException If ftell failed.
     */
    public function getPosition(): int
    {
        $pos = ftell($this->memory);
        if ($pos === false)
        {
            throw new IOException('ftell failed');
        }

        return $pos;
    }

    /**
     * Sets the position in the stream.
     *
     * @param int $position The new position.
     *
     * @throws IOException If fseek failed.
     */
    public function setPosition(int $position): void
    {
        $this->seek($position, SEEK_SET);
    }

    /**
     * Seeks on the stream.
     *
     * @param int $offset The new position within the stream, relative to `$whence` value.
     * @param int $whence The seek reference point.
     *
     * @throws IOException If fseek failed.
     */
    public function seek(int $offset, int $whence): void
    {
        if (fseek($this->memory, $offset, $whence) === -1)
        {
            throw new IOException('fseek failed');
        }
    }

    /**
     * Reads a block of bytes from the stream.
     *
     * @param int $length The maximum number of bytes to read.
     * @param string|null $data The block of bytes read.
     *
     * @return int The number of bytes read.
     * @throws IOException If the read operation failed.
     */
    public function read(int $length, ?string &$data): int
    {
        $data = fread($this->memory, $length);
        if ($data === false)
        {
            $data = null;
            throw new IOException('Failed to read the stream');
        }

        return strlen($data);
    }

    /**
     * Writes a block of bytes to the stream.
     *
     * @param string $data The data to be written.
     * @param int $length The maximum number of bytes to write. If `$length` is less than zero,
     * writing will stop until the end of `$data` is reached.
     *
     * @return int The number of bytes written.
     * @throws IOException If the write operation failed.
     */
    public function write(string $data, int $length = -1): int
    {
        if ($length < 0)
        {
            $length = strlen($data);
        }

        $bytes = fwrite($this->memory, $data, $length);
        if ($bytes === false)
        {
            throw new IOException('Failed to write to the stream');
        }

        return $bytes;
    }

    /**
     * Does nothing since any data written to a MemoryStream object is written into RAM.
     */
    public function flush(): void
    {
    }

    /**
     * Closes the stream.
     */
    public function close(): void
    {
        fclose($this->memory);
    }

    /**
     * Writes the entire contents of the memory stream to another stream.
     *
     *
     * @param Stream $stream The stream to write this memory stream to.
     *
     * @throws IOException
     */
    public function writeTo(Stream $stream): void
    {
        $this->setPosition(0);
        $this->copyTo($stream);
    }
}
