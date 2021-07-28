<?php declare(strict_types=1);
namespace Neko\IO;

use Neko\InvalidOperationException;
use Neko\UnsupportedOperationException;
use function fclose;
use function feof;
use function fgets;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function ftruncate;
use function fwrite;
use function stream_get_contents;
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
        $this->memory = fopen('php://memory', 'r+');
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
        return $this->memory !== null;
    }

    /**
     * Determines whether the stream can be written.
     *
     * @return bool
     */
    public function canWrite(): bool
    {
        return $this->memory !== null;
    }

    /**
     * Determines whether the stream is seekable.
     *
     * @return bool
     */
    public function canSeek(): bool
    {
        return $this->memory !== null;
    }

    /**
     * Determines whether the stream has reached the end.
     *
     * @return bool
     * @throws InvalidOperationException If the stream is closed.
     */
    public function endOfStream(): bool
    {
        $this->ensureStreamIsOpen();
        return feof($this->memory);
    }

    /**
     * Gets the stream size.
     *
     * @return int
     * @throws IOException If fstat failed.
     * @throws InvalidOperationException If the stream is closed.
     */
    public function getSize(): int
    {
        $this->ensureStreamIsOpen();
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
     * @throws InvalidOperationException If the stream is closed.
     */
    public function setSize(int $size): void
    {
        $this->ensureStreamIsOpen();
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
     * @throws InvalidOperationException If the stream is closed.
     */
    public function getPosition(): int
    {
        $this->ensureStreamIsOpen();
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
     * @throws InvalidOperationException If the stream is closed.
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
     * @throws InvalidOperationException If the stream is closed.
     */
    public function seek(int $offset, int $whence): void
    {
        $this->ensureStreamIsOpen();
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
     * @throws InvalidOperationException If the stream is closed.
     */
    public function read(int $length): string
    {
        $this->ensureStreamIsOpen();
        $data = fread($this->memory, $length);
        if ($data === false)
        {
            throw new IOException('Could not read the stream');
        }

        return $data;
    }

    /**
     * Reads a line of characters from the stream.
     *
     * @return string The line of text read.
     * @throws IOException
     * @throws InvalidOperationException If the stream is closed.
     */
    public function readLine(): string
    {
        $this->ensureStreamIsOpen();
        $line = fgets($this->memory);
        if ($line === false)
        {
            throw new IOException('Could not read the stream');
        }

        return $line;
    }

    /**
     * Reads all characters from the current position to the end of the stream.
     *
     * @return string
     * @throws IOException
     * @throws InvalidOperationException If the stream is closed.
     */
    public function readToEnd(): string
    {
        $this->ensureStreamIsOpen();
        $data = stream_get_contents($this->memory);
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
     * @throws InvalidOperationException If the stream is closed.
     */
    public function write(string $data, int $length = -1): int
    {
        $this->ensureStreamIsOpen();
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
     * Writes a string to the stream, followed by a line terminator.
     *
     * @param string $data The string to be written.
     * @param int $length The maximum number of bytes to write. If the value is less than zero, writing will stop
     * until the end of $data is reached. This value does not count the length of the line terminator.
     *
     * @return int The number of bytes written.
     * @throws IOException
     * @throws InvalidOperationException If the stream is closed.
     */
    public function writeLine(string $data, int $length = -1): int
    {
        $bytes = $this->write($data, $length);
        $bytes += $this->write(PHP_EOL, strlen(PHP_EOL));
        return $bytes;
    }

    /**
     * Writes the entire content of the memory stream to another stream.
     *
     * @param Stream $stream The stream to write the contents of this stream to.
     * @param int $buffer_size The size of the buffer. This value must be greater than zero.
     *
     * @throws IOException If this stream is not readable or the destination stream is not writable.
     * @throws UnsupportedOperationException If the stream is not readable or the destination is not writable.
     * @throws InvalidOperationException If the stream is closed.
     */
    public function writeTo(Stream $stream, int $buffer_size = 81920): void
    {
        $this->setPosition(0);
        $this->copyTo($stream, $buffer_size);
    }

    /**
     * Does nothing as the whole stream is written to memory.
     */
    public function flush(): void
    {
    }

    /**
     * Closes the stream.
     */
    public function close(): void
    {
        if ($this->memory !== null)
        {
            fclose($this->memory);
            $this->memory = null;
        }
    }

    /**
     * Throws an exception if the user attempts to do something when the stream is closed.
     *
     * @throws InvalidOperationException
     */
    private function ensureStreamIsOpen(): void
    {
        if ($this->memory === null)
        {
            throw new InvalidOperationException('The stream is closed.');
        }
    }
}
