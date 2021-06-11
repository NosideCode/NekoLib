<?php declare(strict_types=1);
namespace NekoLib\IO;

use NekoLib\UnsupportedOperationException;

/**
 * Provides a generic interface for streams.
 */
abstract class Stream
{
    /**
     * Determines whether the stream can be read.
     *
     * @return bool
     */
    abstract public function canRead(): bool;

    /**
     * Determines whether the stream can be written.
     *
     * @return bool
     */
    abstract public function canWrite(): bool;

    /**
     * Determines whether the stream is seekable.
     *
     * @return bool
     */
    abstract public function canSeek(): bool;

    /**
     * Determines whether the stream has reached the end.
     *
     * @return bool
     */
    abstract public function endOfStream(): bool;

    /**
     * Gets the stream size.
     *
     * @return int
     */
    abstract public function getSize(): int;

    /**
     * Sets the size of the stream.
     *
     * @param int $size The new size. If the new size is less than the current size, the stream will be truncated.
     *
     * @throws IOException
     */
    abstract public function setSize(int $size): void;

    /**
     * Gets the position in the stream.
     *
     * @return int
     * @throws IOException
     */
    abstract public function getPosition(): int;

    /**
     * Sets the position in the stream.
     *
     * @param int $position The new position.
     *
     * @throws IOException
     */
    abstract public function setPosition(int $position): void;

    /**
     * Seeks on the stream.
     *
     * @param int $offset The new position within the stream, relative to $whence value.
     * @param int $whence The seek reference point.
     *
     * @throws IOException
     */
    abstract public function seek(int $offset, int $whence): void;

    /**
     * Reads a block of bytes from the stream.
     *
     * @param int $length The maximum number of bytes to read.
     *
     * @return string The block of bytes read.
     * @throws IOException
     * @throws UnsupportedOperationException
     */
    abstract public function read(int $length): string;

    /**
     * Writes a block of bytes to the stream.
     *
     * @param string $data The data to be written.
     * @param int $length The maximum number of bytes to write. If the value is less than zero, writing will stop
     * until the end of $data is reached.
     *
     * @return int The number of bytes written.
     * @throws IOException
     * @throws UnsupportedOperationException
     */
    abstract public function write(string $data, int $length = -1): int;

    /**
     * Clears the buffer of this stream and forces any buffered data to be written to its destination.
     *
     * @throws IOException
     */
    abstract public function flush(): void;

    /**
     * Closes the stream.
     */
    abstract public function close(): void;

    /**
     * Writes the stream contents into another stream. Copying begins at the current position in the stream
     * and does not reset the position of the destination stream after the copy operation is complete.
     *
     * @param Stream $stream The stream to copy the contents of this stream to.
     * @param int $buffer_size The size of the buffer. This value must be greater than zero.
     *
     * @throws IOException If this stream is not readable or the destination stream is not writable.
     * @throws UnsupportedOperationException If the stream is not readable or the destination stream is not writable.
     */
    public function copyTo(Stream $stream, int $buffer_size = 81920): void
    {
        while (!$this->endOfStream())
        {
            $data = $this->read($buffer_size);
            $stream->write($data);
        }

        $stream->flush();
    }

    /**
     * Ensures that the stream is readable.
     *
     * @throws UnsupportedOperationException
     */
    protected function ensureCanRead(): void
    {
        if (!$this->canRead())
        {
            throw new UnsupportedOperationException('The stream does not support reading');
        }
    }

    /**
     * Ensures that the stream is writable.
     *
     * @throws UnsupportedOperationException
     */
    protected function ensureCanWrite(): void
    {
        if (!$this->canWrite())
        {
            throw new UnsupportedOperationException('The stream does not support writing');
        }
    }
}
