<?php declare(strict_types=1);
namespace Neko\IO;

use Neko\UnauthorizedAccessException;
use function copy;
use function file_exists;
use function filesize;
use function is_dir;
use function is_executable;
use function is_file;
use function is_uploaded_file;
use function unlink;

/**
 * Represents a file.
 */
final class FileInfo extends FileSystemInfo
{
    /**
     * Determines whether the file is executable.
     *
     * @return bool
     */
    public function isExecutable(): bool
    {
        return @is_executable($this->full_path);
    }

    /**
     * Determines whether the file was uploaded via HTTP POST.
     *
     * @return bool
     */
    public function isUploaded(): bool
    {
        return @is_uploaded_file($this->full_path);
    }

    /**
     * Gets the extension of the file.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return Path::getExtension($this->full_path);
    }

    /**
     * Gets the basename of the file without its extension.
     *
     * @return string
     */
    public function getBaseName(): string
    {
        return Path::getFileNameWithoutExtension($this->full_path);
    }

    /**
     * Gets the size of the file.
     *
     * @return int
     * @throws FileNotFoundException
     * @throws IOException If filesize failed to stat the file.
     */
    public function getSize(): int
    {
        $this->ensureExists();
        $size = @filesize($this->full_path);

        if ($size === false)
        {
            $this->throwIOExceptionFromError();
        }

        return $size;
    }

    /**
     * Determines whether the file exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return @is_file($this->full_path);
    }

    /**
     * Creates the file. If the file already exists, it does nothing.
     *
     * @throws IOException If touch failed to create the file.
     */
    public function create(): void
    {
        if (!@touch($this->full_path))
        {
            $this->throwIOExceptionFromError();
        }
    }

    /**
     * Deletes the file. If the file doesn't exist, it does nothing.
     *
     * @throws FileNotFoundException
     * @throws IOException If unlink failed to delete the file.
     * @throws UnauthorizedAccessException If the file cannot be deleted.
     */
    public function delete(): void
    {
        if ($this->exists())
        {
            $this->ensureIsWritable();
            if (!@unlink($this->full_path))
            {
                $this->throwIOExceptionFromError();
            }
        }
    }

    /**
     * Copies the file.
     *
     * @param string $destFileName The path to copy the file to, which can specify a different file name.
     * @param bool $overwrite True to allow an existing file to be overwritten; otherwise, False.
     *
     * @return FileInfo A new FileInfo instance for the copied file.
     * @throws DirectoryNotFoundException If the destination directory does not exist.
     * @throws FileNotFoundException
     * @throws IOException An error occurs, or the destination file already exists and overwrite is False.
     * @throws UnauthorizedAccessException If the destination directory cannot be modified.
     */
    public function copyTo(string $destFileName, bool $overwrite = false): FileInfo
    {
        $this->ensureIsReadable();
        $destFileName = Path::getFullPath($destFileName);
        $this->checkDestination($destFileName, $overwrite);

        if (!@copy($this->full_path, $destFileName))
        {
            $this->throwIOExceptionFromError();
        }

        return new FileInfo($destFileName);
    }

    /**
     * Moves the file to a different path.
     *
     * @param string $destFileName The path to move the file or directory to, which can specify a different file name.
     * @param bool $overwrite True to overwrite the destination file if it already exists.
     *
     * @throws DirectoryNotFoundException If the destination directory does not exist.
     * @throws FileNotFoundException
     * @throws IOException An error occurs, or the destination file already exists and overwrite is False.
     * @throws UnauthorizedAccessException If the destination directory cannot be modified.
     */
    public function moveTo(string $destFileName, bool $overwrite = false): void
    {
        $this->ensureIsWritable();
        $destFileName = Path::getFullPath($destFileName);
        $this->checkDestination($destFileName, $overwrite);

        if (!@rename($this->full_path, $destFileName))
        {
            $this->throwIOExceptionFromError();
        }

        $this->original_path = $destFileName;
        $this->full_path = $destFileName;
    }

    /**
     * Common checks for copyTo and moveTo methods.
     *
     * @param string $destination
     * @param bool $overwrite
     *
     * @throws DirectoryNotFoundException
     * @throws IOException
     */
    private function checkDestination(string $destination, bool $overwrite): void
    {
        $dir = Path::getDirectoryName($destination);
        if (!is_dir($dir))
        {
            throw new DirectoryNotFoundException('Could not find a part of the path');
        }

        // Check for overwrite
        if (!$overwrite && file_exists($destination))
        {
            throw new IOException("File \"$destination\" already exists");
        }
    }
}
