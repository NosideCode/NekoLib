<?php declare(strict_types=1);
namespace Neko\IO;

use Neko\UnauthorizedAccessException;
use function is_dir;
use function is_file;
use function is_link;
use function mkdir;
use function rmdir;
use function scandir;

/**
 * Represents a directory.
 */
final class DirectoryInfo extends FileSystemInfo
{
    /**
     * @return DirectoryInfo[]
     * @throws FileNotFoundException
     */
    public function getDirectories(): array
    {
        $this->ensureExists();
        $directoryList = [];

        foreach (scandir($this->full_path) as $dir)
        {
            if ($dir === '.' || $dir === '..')
            {
                continue;
            }

            $path = Path::combine($this->full_path, $dir);
            if (@is_dir($path) && !@is_link($path))
            {
                $directoryList[] = new DirectoryInfo($path);
            }
        }

        return $directoryList;
    }

    /**
     * @return FileInfo[]
     * @throws FileNotFoundException
     */
    public function getFiles(): array
    {
        $this->ensureExists();
        $fileList = [];

        foreach (scandir($this->full_path) as $file)
        {
            if ($file === '.' || $file === '..')
            {
                continue;
            }

            $path = Path::combine($this->full_path, $file);
            if (@is_file($path))
            {
                $fileList[] = new FileInfo($path);
            }
        }

        return $fileList;
    }

    /**
     * @return FileSystemInfo[]
     * @throws FileNotFoundException
     */
    public function getFileSystemInfos(): array
    {
        $this->ensureExists();
        $fileList = [];

        foreach (scandir($this->full_path) as $file)
        {
            if ($file === '.' || $file === '..')
            {
                continue;
            }

            $path = Path::combine($this->full_path, $file);
            if (@is_dir($path) && !@is_link($path))
            {
                $fileList[] = new DirectoryInfo($path);
            }
            else
            {
                $fileList[] = new FileInfo($path);
            }
        }

        return $fileList;
    }

    /**
     * Determines whether the file or directory exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return @is_dir($this->full_path);
    }

    /**
     * Creates the directory. If the directory already exists, it does nothing.
     *
     * @throws IOException If mkdir failed to create the directory.
     */
    public function create(): void
    {
        if (!$this->exists())
        {
            if (!@mkdir($this->full_path))
            {
                self::throwIOExceptionFromError();
            }
        }
    }

    /**
     * Deletes the directory. If the directory doesn't exist, it does nothing.
     *
     * @param bool $recursive If the directory should be deleted recursively.
     *
     * @throws FileNotFoundException
     * @throws IOException
     * @throws UnauthorizedAccessException
     */
    public function delete(bool $recursive = false): void
    {
        if ($this->exists())
        {
            $this->ensureIsWritable();
            self::deleteRecursive($this, $recursive);
        }
    }

    /**
     * Copies the directory.
     *
     * @param string $destFileName The path to copy the directory to, which can specify a different file name.
     * @param bool $overwrite True to allow an existing file to be overwritten; otherwise, False.
     * @param bool $recursive True to allow recursive copy.
     *
     * @return DirectoryInfo A DirectoryInfo instance for the copied directory.
     * @throws DirectoryNotFoundException If the destination directory does not exist.
     * @throws FileNotFoundException
     * @throws IOException
     * @throws UnauthorizedAccessException If the directory cannot be modified.
     */
    public function copyTo(string $destFileName, bool $overwrite = false, bool $recursive = false): DirectoryInfo
    {
        $this->ensureIsReadable();
        $fullDestFileName = Path::getFullPath($destFileName);
        if (!is_dir(Path::getDirectoryName($fullDestFileName)))
        {
            throw new DirectoryNotFoundException('Could not find a part of the path');
        }

        self::copyRecursive($this, $fullDestFileName, $overwrite, $recursive);
        $copied = clone $this;
        $copied->original_path = $destFileName;
        $copied->full_path = $fullDestFileName;
        return $copied;
    }

    /**
     * Moves the directory to a different path.
     *
     * @param string $destFileName The path to move the directory to, which can specify a different file name.
     * @param bool $overwrite True to overwrite an existing file to be overwritten; otherwise, False.
     * @param bool $recursive True to allow recursive move.
     *
     * @throws DirectoryNotFoundException
     * @throws FileNotFoundException
     * @throws IOException
     * @throws UnauthorizedAccessException If the directory cannot be modified.
     */
    public function moveTo(string $destFileName, bool $overwrite = false, bool $recursive = false): void
    {
        $this->ensureIsWritable();
        $fullDestFileName = Path::getFullPath($destFileName);
        if (!is_dir(Path::getDirectoryName($fullDestFileName)))
        {
            throw new DirectoryNotFoundException('Could not find a part of the path');
        }

        self::moveRecursive($this, $fullDestFileName, $overwrite, $recursive);
        $this->original_path = $destFileName;
        $this->full_path = $fullDestFileName;
    }

    /**
     * Copies a directory recursively.
     *
     * @param DirectoryInfo $source
     * @param string $destination
     * @param bool $overwrite
     * @param bool $recursive
     *
     * @throws FileNotFoundException
     * @throws IOException
     */
    private static function copyRecursive(DirectoryInfo $source, string $destination, bool $overwrite, bool $recursive): void
    {
        (new DirectoryInfo($destination))->create();
        if ($recursive)
        {
            foreach ($source->getFileSystemInfos() as $fsi)
            {
                $target = Path::combine($destination, $fsi->getName());
                if ($fsi instanceof DirectoryInfo)
                {
                    self::copyRecursive($fsi, $target, $overwrite, $recursive);
                }
                else
                {
                    $fsi->copyTo($target, $overwrite);
                }
            }
        }
    }

    /**
     * Moves a directory recursively.
     *
     * @param DirectoryInfo $source
     * @param string $destination
     * @param bool $overwrite
     * @param bool $recursive
     *
     * @throws FileNotFoundException
     * @throws IOException
     */
    private static function moveRecursive(DirectoryInfo $source, string $destination, bool $overwrite, bool $recursive): void
    {
        (new DirectoryInfo($destination))->create();
        if ($recursive)
        {
            foreach ($source->getFileSystemInfos() as $fsi)
            {
                $target = Path::combine($destination, $fsi->getName());
                if ($fsi instanceof DirectoryInfo)
                {
                    self::moveRecursive($fsi, $target, $overwrite, $recursive);
                }
                else
                {
                    $fsi->moveTo($target, $overwrite);
                }
            }
        }

        // Delete the source directory
        if (!@rmdir($source->full_path))
        {
            self::throwIOExceptionFromError();
        }
    }

    /**
     * Deletes a directory recursively.
     *
     * @param DirectoryInfo $dir
     * @param bool $recursive
     *
     * @throws FileNotFoundException
     * @throws IOException
     */
    private static function deleteRecursive(DirectoryInfo $dir, bool $recursive): void
    {
        if ($recursive)
        {
            foreach ($dir->getFileSystemInfos() as $fsi)
            {
                if ($fsi instanceof DirectoryInfo)
                {
                    self::deleteRecursive($fsi, $recursive);
                }
                else
                {
                    $fsi->delete();
                }
            }
        }

        // Remove the empty directory
        if (!@rmdir($dir->full_path))
        {
            self::throwIOExceptionFromError();
        }
    }
}
