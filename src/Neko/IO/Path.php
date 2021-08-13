<?php declare(strict_types=1);
namespace Neko\IO;

use InvalidArgumentException;
use function func_num_args;
use function getcwd;
use function str_contains;
use function str_starts_with;
use function strlen;
use function strrpos;
use function substr;
use const DIRECTORY_SEPARATOR;

/**
 * Provides methods for processing path strings.
 * These operations are performed in a cross-platform manner.
 */
final class Path
{
    /**
     * Determines whether the given character is a directory separator.
     *
     * @param string $char The char to test.
     *
     * @return bool
     */
    public static function isDirectorySeparator(string $char): bool
    {
        if (self::isWindows())
        {
            return $char === '\\' || $char === '/';
        }

        return $char === DIRECTORY_SEPARATOR;
    }

    /**
     * Determines whether the given path ends with a directory separator.
     *
     * @param string $path The path to test.
     *
     * @return bool
     */
    public static function endsInDirectorySeparator(string $path): bool
    {
        $lastChar = substr($path, -1, 1);
        return self::isDirectorySeparator($lastChar);
    }

    /**
     * Gets the directory name part of the path.
     *
     * @param string $path The path from which to obtain the directory name.
     *
     * @return string
     */
    public static function getDirectoryName(string $path): string
    {
        $rootLen = self::getRootLength($path);
        $end = strlen($path);

        if ($end <= $rootLen)
        {
            return '';
        }

        while ($end > $rootLen)
        {
            if (self::isDirectorySeparator($path[--$end]))
            {
                break;
            }
        }

        // Trim any extra directory separator
        while ($end > $rootLen && self::isDirectorySeparator($path[$end - 1]))
        {
            $end--;
        }

        return substr($path, 0, $end);
    }

    /**
     * Gets the file name part of the path.
     *
     * @param string $path The path from which to obtain the file name and extension.
     *
     * @return string
     */
    public static function getFileName(string $path): string
    {
        $len = strlen($path) - 1;
        $rootLen = self::getRootLength($path);

        for ($i = $len; $i >= 0; $i--)
        {
            if ($i < $rootLen || self::isDirectorySeparator($path[$i]))
            {
                return substr($path, $i + 1, $len - 1);
            }
        }

        return $path;
    }

    /**
     * Gets the file name without extension.
     *
     * @param string $path The path from which to obtain the file name.
     *
     * @return string
     */
    public static function getFileNameWithoutExtension(string $path): string
    {
        $name = self::getFileName($path);
        $ext = strrpos($name, '.');
        return $ext === false ? $name : substr($name, 0, $ext);
    }

    /**
     * Determines whether the path has a file extension.
     *
     * @param string $path The path to test.
     *
     * @return bool
     */
    public static function hasExtension(string $path): bool
    {
        $len = strlen($path) - 1;
        for ($i = $len; $i >= 0; $i--)
        {
            $chr = $path[$i];
            if ($chr === '.')
            {
                // 'file.' => false
                // 'file.php' => true
                return $i !== $len;
            }

            if (self::isDirectorySeparator($chr))
            {
                break;
            }
        }

        return false;
    }

    /**
     * Gets the file extension (including the period) of the path.
     *
     * @param string $path The path from which to obtain the file extension.
     *
     * @return string The extension of the path or an empty string if the path is empty or does not have an extension.
     */
    public static function getExtension(string $path): string
    {
        $len = strlen($path);
        for ($i = $len - 1; $i >= 0; $i--)
        {
            $chr = $path[$i];
            if ($chr === '.' && $i !== $len - 1)
            {
                return substr($path, $i, $len - $i);
            }
        }

        // path does not have an extension
        return '';
    }

    /**
     * Changes the file extension of a path.
     *
     * @param string $path The path to change.
     * @param string|null $extension The new file extension (with or without a period).
     * If NULL is passed, the extension will be removed from the path.
     *
     * @return string
     */
    public static function changeExtension(string $path, ?string $extension): string
    {
        $len = strlen($path);
        if ($len === 0)
        {
            return '';
        }

        for ($i = $len - 1; $i >= 0; $i--)
        {
            $chr = $path[$i];
            if ($chr === '.')
            {
                $len = $i;
                break;
            }

            if (self::isDirectorySeparator($chr))
            {
                break;
            }
        }

        $fileNoExt = substr($path, 0, $len);
        if ($extension === null)
        {
            return $fileNoExt;
        }

        if (!str_starts_with($extension, '.'))
        {
            $extension = '.' . $extension;
        }

        return $fileNoExt . $extension;
    }

    /**
     * Determines whether the path has a root.
     *
     * @param string $path The path to test.
     *
     * @return bool
     */
    public static function isPathRooted(string $path): bool
    {
        return self::getRootLength($path) > 0;
    }

    /**
     * Gets the root part of the path.
     *
     * @param string $path The path from which to obtain the root.
     *
     * @return string
     */
    public static function getPathRoot(string $path): string
    {
        return substr($path, 0, self::getRootLength($path));
    }

    /**
     * Concatenates a list of paths into a single path.
     *
     * @param string ...$paths The list of paths to concatenate.
     *
     * @return string The combined path. If an argument other than the first contains a rooted path, all previous
     * values are ignored and the resulting path begins with that rooted value.
     */
    public static function combine(string ...$paths): string
    {
        $numArgs = func_num_args();
        $start = $numArgs;

        if ($numArgs === 0)
        {
            return '';
        }

        while ($start > 0)
        {
            if (self::isPathRooted($paths[--$start]))
            {
                break;
            }
        }

        $result = '';
        for ($i = $start; $i < $numArgs; $i++)
        {
            $path = $paths[$i];
            if (strlen($path) === 0)
            {
                continue;
            }

            if ($i + 1 < $numArgs && !self::endsInDirectorySeparator($path))
            {
                $path .= DIRECTORY_SEPARATOR;
            }

            $result .= $path;
        }

        return $result;
    }

    /**
     * Concatenates a list of paths into a single path.
     *
     * @param string ...$paths The list of paths to concatenate.
     *
     * @return string The concatenated path. Unlike combine(), this method does not look for a rooted path and only
     * concatenates by adding directory separators when necessary.
     */
    public static function join(string ...$paths): string
    {
        $numArgs = func_num_args();
        if ($numArgs === 0)
        {
            return '';
        }

        $result = '';
        for ($i = 0; $i < $numArgs; $i++)
        {
            $path = $paths[$i];
            if (strlen($path) === 0)
            {
                continue;
            }

            if ($i + 1 < $numArgs && !self::endsInDirectorySeparator($path))
            {
                $path .= DIRECTORY_SEPARATOR;
            }

            $result .= $path;
        }

        return $result;
    }

    /**
     * Normalizes the directory separator with the correct character
     * and removes duplicated separators.
     *
     * @param string $path The path to normalize.
     *
     * @return string
     */
    public static function normalize(string $path): string
    {
        $len = strlen($path);
        $normalized = '';

        for ($i = 0; $i < $len; $i++)
        {
            $chr = $path[$i];
            if (self::isDirectorySeparator($chr))
            {
                $chr = DIRECTORY_SEPARATOR;
                // Ignore repeated directory separators
                // "/home//foo/bar" => "/home/foo/bar"
                if ($i + 1 < $len && self::isDirectorySeparator($path[$i + 1]))
                {
                    continue;
                }
            }

            $normalized .= $chr;
        }

        return $normalized;
    }

    /**
     * Gets the absolute path.
     *
     * @param string $path The path to resolve.
     *
     * @return string The absolute or canonical path. Unlike php's realpath() function, this method does not
     * check if the path exists.
     * @throws InvalidArgumentException If the path is empty or contains illegal characters
     */
    public static function getFullPath(string $path): string
    {
        if (strlen($path) === 0)
        {
            throw new InvalidArgumentException('Path is empty');
        }

        if (str_contains($path, "\0"))
        {
            throw new InvalidArgumentException('Path contains illegal characters');
        }

        if (!self::isPathRooted($path))
        {
            $path = self::join(getcwd(), $path);
        }

        $resolved = self::removeDots($path, self::getRootLength($path));
        return strlen($resolved) === 0 ? DIRECTORY_SEPARATOR : $resolved;
    }

    /**
     * Removes relative segments from the given path.
     *
     * @param string $path The path to resolve.
     * @param int $rootLen The length of the root of the given path.
     *
     * @return string
     */
    private static function removeDots(string $path, int $rootLen): string
    {
        $path = self::normalize($path);
        $len = strlen($path);
        $skip = $rootLen;
        $canonical = '';

        // Treat "/..", "/." or "//" as relative segments.
        // In cases like "\\?\C:\.\" and "\\?\C:\..\", the first segment after the root will be ".\" and "..\"
        if (self::isDirectorySeparator($path[$skip - 1]))
        {
            $skip--;
        }

        // Remove "//", "/./", and "/../"
        if ($skip > 0)
        {
            $canonical = substr($path, 0, $skip);
        }

        for ($i = $skip; $i < $len; $i++)
        {
            $c = $path[$i];
            if (self::isDirectorySeparator($c) && $i + 1 < $len)
            {
                // Skip current directory dot.
                // "parent/./child" => "parent/child"
                if (($i + 2 === $len || self::isDirectorySeparator($path[$i + 2])) && $path[$i + 1] === '.')
                {
                    $i++;
                    continue;
                }

                // Resolve parent directory dots.
                // "parent/child/../grandchild" => "parent/grandchild"
                if ($i + 2 < $len && ($i + 3 === $len || self::isDirectorySeparator($path[$i + 3])) && $path[$i + 1] === '.' && $path[$i + 2] === '.')
                {
                    for ($s = strlen($canonical) - 1; $s >= $skip; $s--)
                    {
                        if (self::isDirectorySeparator($canonical[$s]))
                        {
                            // Avoid removing the complete "\tmp\" segment in cases like \\?\C:\tmp\..\, C:\tmp\..
                            $length = ($i + 3 >= $len && $s === $skip) ? $s + 1 : $s;
                            $canonical = substr($canonical, 0, $length);
                            break;
                        }
                    }

                    if ($s < $skip)
                    {
                        $canonical = substr($canonical, 0, $skip);
                    }

                    $i += 2;
                    continue;
                }
            }

            $canonical .= $c;
        }

        // We may have eaten the trailing separator from the root when we started and not replaced it
        if ($skip !== $rootLen && strlen($canonical) < $rootLen)
        {
            $canonical .= $path[$rootLen - 1];
        }

        return $canonical;
    }

    /**
     * Gets the root length for the given path.
     *
     * "/home/user' => 1 (root="/")
     * "C:\Windows" => 3 (root="C:\")
     * "\\server-name\share-name\file" => 25 (root="\\server-name\share-name\")
     *
     * @param string $path
     *
     * @return int
     */
    private static function getRootLength(string $path): int
    {
        if (self::isWindows())
        {
            return self::getWindowsRootLength($path);
        }

        return strlen($path) >= 1 && $path[0] === DIRECTORY_SEPARATOR ? 1 : 0;
    }

    /**
     * Gets the root length for a Windows path.
     * This method should not be called directly, always call getRootLength() instead.
     *
     * @param string $path
     *
     * @return int
     */
    private static function getWindowsRootLength(string $path): int
    {
        $idx = 0;
        $len = strlen($path);

        if ($len >= 1 && self::isDirectorySeparator($path[0]))
        {
            // Handles UNC names and directories off current drive's root
            $idx = 1;
            if ($len >= 2 && self::isDirectorySeparator($path[1]))
            {
                $idx = 2;
                $n = 2;

                while ($idx < $len && (!self::isDirectorySeparator($path[$idx]) || --$n > 0))
                {
                    $idx++;
                }
            }
        }
        else
        {
            if ($len >= 2 && $path[1] === ':')
            {
                // Handles "C:\Foo"
                $idx = 2;
                if ($len >= 3 && self::isDirectorySeparator($path[2]))
                {
                    $idx++;
                }
            }
        }

        return $idx;
    }

    /**
     * Determines whether PHP is running on a Windows machine.
     *
     * @return bool
     */
    private static function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * Static class.
     */
    private function __construct()
    {
    }
}
