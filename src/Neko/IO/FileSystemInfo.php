<?php declare(strict_types=1);
namespace Neko\IO;

use InvalidArgumentException;
use Neko\UnauthorizedAccessException;
use function error_clear_last;
use function error_get_last;
use function fileatime;
use function filectime;
use function filemtime;
use function is_link;
use function is_readable;
use function is_writable;
use function time;
use function touch;

/**
 * Represents an abstract interface for filesystem information.
 */
abstract class FileSystemInfo
{
    protected string $original_path;
    protected string $full_path;

    /**
     * FileSystemInfo constructor.
     *
     * @param string $path The path to a file or directory.
     */
    public function __construct(string $path)
    {
        $this->original_path = $path;
        $this->full_path = Path::getFullPath($path);
    }

    /**
     * Determines whether the file or directory is readable.
     *
     * @return bool
     */
    final public function isReadable(): bool
    {
        return @is_readable($this->full_path);
    }

    /**
     * Determines whether the file or directory is writable.
     *
     * @return bool
     */
    final public function isWritable(): bool
    {
        return @is_writable($this->full_path);
    }

    /**
     * Determines whether the file or directory is a symbolic link.
     *
     * @return bool
     */
    final public function isLink(): bool
    {
        return @is_link($this->full_path);
    }

    /**
     * Gets the original path passed to the constructor.
     *
     * @return string
     */
    final public function getPath(): string
    {
        return $this->original_path;
    }

    /**
     * Gets the canonical full path.
     *
     * @return string
     */
    final public function getFullPath(): string
    {
        return $this->full_path;
    }

    /**
     * Gets the name of the file or directory.
     *
     * @return string
     */
    final public function getName(): string
    {
        return Path::getFileName($this->full_path);
    }

    /**
     * Gets the parent directory path.
     *
     * @return string
     */
    final public function getDirectoryName(): string
    {
        return Path::getDirectoryName($this->full_path);
    }

    /**
     * Gets the last modification time.
     *
     * @return int
     * @throws FileNotFoundException
     * @throws IOException If filemtime failed to stat the file.
     */
    public function getLastModifiedTime(): int
    {
        $this->ensureExists();
        $time = @filemtime($this->full_path);

        if ($time === false)
        {
            self::throwIOExceptionFromError();
        }

        return $time;
    }

    /**
     * Sets the last modification time.
     *
     * @param int $mtime The modification time.
     *
     * @throws FileNotFoundException
     * @throws IOException If touch fails.
     * @throws UnauthorizedAccessException If the php process has not permission to modify the file.
     */
    public function setLastModifiedTime(int $mtime): void
    {
        $this->ensureIsWritable();
        if (!@touch($this->full_path, $mtime))
        {
            self::throwIOExceptionFromError();
        }
    }

    /**
     * Gets the last access time.
     *
     * @return int
     * @throws FileNotFoundException
     * @throws IOException If fileatime failed to stat the file.
     */
    public function getLastAccessTime(): int
    {
        $this->ensureExists();
        $time = @fileatime($this->full_path);

        if ($time === false)
        {
            self::throwIOExceptionFromError();
        }

        return $time;
    }

    /**
     * Sets the last access time.
     *
     * @param int $atime The access time.
     *
     * @throws FileNotFoundException
     * @throws IOException If touch fails.
     * @throws UnauthorizedAccessException If the php process has not permission to modify the file.
     */
    public function setLastAccessTime(int $atime): void
    {
        $this->ensureIsWritable();
        if (!@touch($this->full_path, time(), $atime))
        {
            self::throwIOExceptionFromError();
        }
    }

    /**
     * Gets the inode change time. On Windows, the creation time is returned.
     *
     * @return int
     * @throws FileNotFoundException
     * @throws IOException If filectime failed to stat the file.
     */
    public function getCreateTime(): int
    {
        $this->ensureExists();
        $time = @filectime($this->full_path);

        if ($time === false)
        {
            self::throwIOExceptionFromError();
        }

        return $time;
    }

    /**
     * Determines whether the file or directory exists.
     *
     * @return bool
     */
    abstract public function exists(): bool;

    /**
     * Creates a new file or directory.
     * If it already exists, it does nothing.
     */
    abstract public function create(): void;

    /**
     * Deletes the file or directory.
     */
    abstract public function delete(): void;

    /**
     * Copies the file or directory.
     *
     * @param string $destFileName The path to copy the file to, which can specify a different file name.
     * @param bool $overwrite True to allow an existing file to be overwritten; otherwise, False.
     *
     * @return FileSystemInfo A new FileSystemInfo instance for the copied file or directory.
     */
    abstract public function copyTo(string $destFileName, bool $overwrite = false): FileSystemInfo;

    /**
     * Moves the file or directory to a different path.
     *
     * @param string $destFileName The path to move the file or directory to, which can specify a different file name.
     * @param bool $overwrite True to overwrite the destination file if it already exists.
     */
    abstract public function moveTo(string $destFileName, bool $overwrite = false): void;

    /**
     * Renames the file or directory.
     *
     * @param string $new_name The new name.
     * @param bool $overwrite True to overwrite the destination file if it already exists.
     *
     * @throws InvalidArgumentException If the new name is not a valid filename.
     * @throws FileNotFoundException
     * @throws UnauthorizedAccessException If the file is not writable.
     */
    public function renameTo(string $new_name, bool $overwrite = false): void
    {
        $this->ensureIsWritable();

        // Ensure it is a filename
        if (Path::getFileName($new_name) !== $new_name)
        {
            throw new InvalidArgumentException('The new name must be a valid file name');
        }

        $fullNewPath = Path::combine($this->getDirectoryName(), $new_name);
        $this->moveTo($fullNewPath, $overwrite);
    }

    /**
     * Throws an IOException using the most recent error message.
     *
     * @throws IOException
     */
    protected static function throwIOExceptionFromError(): void
    {
        $error = error_get_last();
        if ($error !== null)
        {
            $message = $error['message'];
            $type = $error['type'];
            error_clear_last();
            throw new IOException($message, $type);
        }
    }

    /**
     * Ensures that the file or directory exists before attempting to perform any operation.
     *
     * @throws FileNotFoundException
     */
    protected function ensureExists(): void
    {
        if (!$this->exists())
        {
            throw new FileNotFoundException("Could not find file \"$this->full_path\"");
        }
    }

    /**
     * Ensures that the file or directory exists and is readable.
     *
     * @throws FileNotFoundException
     * @throws UnauthorizedAccessException If the file exists but is not readable.
     */
    protected function ensureIsReadable(): void
    {
        $this->ensureExists();
        if (!$this->isReadable())
        {
            throw new UnauthorizedAccessException("Access to the file \"$this->full_path\" is denied");
        }
    }

    /**
     * Ensures that the file or directory exists and is writable.
     *
     * @throws FileNotFoundException
     * @throws UnauthorizedAccessException If the file exists but is not readable.
     */
    protected function ensureIsWritable(): void
    {
        $this->ensureExists();
        if (!$this->isWritable())
        {
            throw new UnauthorizedAccessException("Access to the file \"$this->full_path\" is denied");
        }
    }
}
