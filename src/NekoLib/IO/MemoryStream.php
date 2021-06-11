<?php declare(strict_types=1);
namespace NekoLib\IO;

use NekoLib\UnsupportedOperationException;
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
            throw new IOException('Could not open the memory stream');
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
     * Determines whether the stream is seekable.
     *
     * @return bool
     */
    public function canSeek(): bool
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
     * Gets the stream size.
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
     * @param int $size The new size. If the new size is less than the current size, the stream will be truncated.
     *
     * @throws IOException If ftruncate failed.
     */
    public function setSize(int $size): void
    {
        if (!ftruncate($this->memory, $size))
        {
            throw new IOException('ftruncate failed');
        }
    }

    /**
     * Gets the position in the stream.
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
     * @param int $offset The new position within the stream, relative to $whence value.
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
     *
     * @return string The block of bytes read.
     * @throws IOException
     */
    public function read(int $length): string
    {
        $data = fread($this->memory, $length);
        if ($data === false)
        {
            throw new IOException('Could not read the stream');
        }

        return $data;
    }

    /**
     * Writes a block of bytes to the stream.
     *
     * @param string $data The data to be written.
     * @param int $length The maximum number of bytes to write. If the value is less than zero, writing will stop
     * until the end of $data is reached.
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
            throw new IOException('Could not write to the stream');
        }

        return $bytes;
    }

    /**
     * Does nothing since any data is directly written to memory.
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
     * Writes the entire content of the memory stream to another stream.
     *
     * @param Stream $stream The stream to write the contents of this stream to.
     * @param int $buffer_size The size of the buffer. This value must be greater than zero.
     *
     * @throws IOException If this stream is not readable or the destination stream is not writable.
     * @throws UnsupportedOperationException If the stream is not readable or the destination is not writable.
     */
    public function writeTo(Stream $stream, int $buffer_size = 81920): void
    {
        $this->setPosition(0);
        $this->copyTo($stream, $buffer_size);
    }
}
