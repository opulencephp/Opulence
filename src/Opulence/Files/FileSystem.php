<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Files;

use DateTime;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Defines methods for interacting with the file system
 */
class FileSystem
{
    /**
     * Appends contents to a file
     *
     * @param string $path The path to the file to append to
     * @param mixed $data The string, array, or stream source to write to the file
     * @return int|bool The number of bytes written if successful, otherwise false
     * @throws FileSystemException Thrown if there was a problem appending to the file
     */
    public function append($path, $data)
    {
        return $this->write($path, $data, FILE_APPEND);
    }

    /**
     * Copies directories to a new path
     *
     * @param string $source The path to copy from
     * @param string $target The path to copy to
     * @param null|int $flags The file permissions to use for the new directory(ies)
     * @return bool True if successful, otherwise false
     */
    public function copyDirectory($source, $target, $flags = null)
    {
        if (!$this->exists($source)) {
            return false;
        }

        if (!$this->isDirectory($target)) {
            if (!$this->makeDirectory($target, 0777, true)) {
                return false;
            }
        }

        if ($flags === null) {
            $flags = FilesystemIterator::SKIP_DOTS;
        }

        $items = new FilesystemIterator($source, $flags);

        foreach ($items as $item) {
            if ($item->isDir()) {
                if (!$this->copyDirectory($item->getRealPath(), $target . "/" . $item->getBasename(), $flags)) {
                    return false;
                }
            } elseif ($item->isFile()) {
                if (!$this->copyFile($item->getRealPath(), $target . "/" . $item->getBasename())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Copies a file to a new location
     *
     * @param string $source The path to copy from
     * @param string $target The path to copy to
     * @return bool True if successful, otherwise false
     */
    public function copyFile($source, $target)
    {
        return copy($source, $target);
    }

    /**
     * Recursively deletes a directory
     *
     * @param string $path The path to the directory to delete
     * @param bool $keepDirectoryStructure True if we want to keep the directory structure, otherwise false
     * @return bool True if successful, otherwise false
     */
    public function deleteDirectory($path, $keepDirectoryStructure = false)
    {
        if (!$this->isDirectory($path)) {
            return false;
        }

        $items = new FilesystemIterator($path);

        foreach ($items as $item) {
            if ($item->isDir()) {
                if (!$this->deleteDirectory($item->getRealPath())) {
                    return false;
                }
            } elseif ($item->isFile()) {
                if (!$this->deleteFile($item->getRealPath())) {
                    return false;
                }
            }
        }

        if (!$keepDirectoryStructure) {
            return rmdir($path);
        }

        return true;
    }

    /**
     * Deletes a file
     *
     * @param string $path The file to delete
     * @return bool True if successful, otherwise false
     */
    public function deleteFile($path)
    {
        return @unlink($path);
    }

    /**
     * Gets whether or not a file/directory exists
     *
     * @param string $path The path to check
     * @return bool Whether or not the file/directory exists
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Gets the basename of a path
     *
     * @param string $path The path to check
     * @return string The basename of the path
     * @throws FileSystemException Thrown if the path does not exist
     */
    public function getBasename($path)
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("Path $path not found");
        }

        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Gets all of the directories at the input path
     *
     * @param string $path The path to check
     * @param bool $isRecursive Whether or not we should recurse through child directories
     * @return array All of the directories at the path
     */
    public function getDirectories($path, $isRecursive = false)
    {
        if (!$this->isDirectory($path)) {
            return [];
        }

        $directories = [];
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        if (!$isRecursive) {
            $iter->setMaxDepth(0);
        }

        foreach ($iter as $path => $item) {
            if ($item->isDir()) {
                $directories[] = $path;
            }
        }

        return $directories;
    }

    /**
     * Gets the directory name of a file
     *
     * @param string $path The path to check
     * @return string The directory name of the file
     * @throws FileSystemException Thrown if the file does not exist
     */
    public function getDirectoryName($path)
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("File at path $path not found");
        }

        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Gets the extension of a file
     *
     * @param string $path The path to check
     * @return string The extension of the file
     * @throws FileSystemException Thrown if the file does not exist
     */
    public function getExtension($path)
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("File at path $path not found");
        }

        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Gets the file name of a file
     *
     * @param string $path The path to check
     * @return string The file name
     * @throws FileSystemException Thrown if the file does not exist
     */
    public function getFileName($path)
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("File at path $path not found");
        }

        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Gets the size of a file
     *
     * @param string $path The path to check
     * @return int The number of bytes the file has
     * @throws FileSystemException Thrown if the file does not exist
     */
    public function getFileSize($path)
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("File at path $path not found");
        }

        $fileSize = filesize($path);

        if ($fileSize === false) {
            throw new FileSystemException("Failed to get file size of $path");
        }

        return $fileSize;
    }

    /**
     * Gets all of the files at the input path
     *
     * @param string $path The path to check
     * @param bool $isRecursive Whether or not we should recurse through child directories
     * @return array All of the files at the path
     */
    public function getFiles($path, $isRecursive = false)
    {
        if (!$this->isDirectory($path)) {
            return [];
        }

        $files = [];
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        if (!$isRecursive) {
            $iter->setMaxDepth(0);
        }

        foreach ($iter as $path => $item) {
            if ($item->isFile()) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Gets the last modified time
     *
     * @param string $path The path to check
     * @return DateTime The last modified time
     * @throws FileSystemException Thrown if the file was not found or if the modified time was not readable
     */
    public function getLastModified($path)
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("File at path $path not found");
        }

        $modifiedTimestamp = filemtime($path);

        if ($modifiedTimestamp === false) {
            throw new FileSystemException("Failed to get last modified time of $path");
        }

        return DateTime::createFromFormat("U", $modifiedTimestamp);
    }

    /**
     * Finds files that match a pattern
     *
     * @link http://php.net/manual/function.glob.php
     *
     * @param string $pattern The pattern to match on
     * @param int $flags The glob flags to use
     * @return array The list of matched files
     * @throws FileSystemException Thrown if the search failed
     */
    public function glob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        if ($files === false) {
            throw new FileSystemException("Glob failed for pattern \"$pattern\" with flags $flags");
        }

        return $files;
    }

    /**
     * Gets whether or not a path points to a directory
     *
     * @param string $path The path to check
     * @return bool True if the path points a directory, otherwise false
     */
    public function isDirectory($path)
    {
        return is_dir($path);
    }

    /**
     * Gets whether or not a path points to a file
     *
     * @param string $path The path to check
     * @return bool True if the path points to a file, otherwise false
     */
    public function isFile($path)
    {
        return is_file($path);
    }

    /**
     * Gets whether or not a path is readable
     *
     * @param string $path The path to check
     * @return bool True if the path is readable, otherwise false
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Gets whether or not a path is writable
     *
     * @param string $path The path to check
     * @return bool True if the path is writable, otherwise false
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Makes a directory at the input path
     *
     * @param string $path The path to the directory to make
     * @param int $mode The chmod permissions
     * @param bool $isRecursive Whether or not we create nested directories
     * @return bool True if successful, otherwise false
     */
    public function makeDirectory($path, $mode = 0777, $isRecursive = false)
    {
        $result = mkdir($path, $mode, $isRecursive);
        // The directory might not get the correct mode due to umask, so we have to chmod it
        chmod($path, $mode);

        return $result;
    }

    /**
     * Moves a file from one location to another
     * Analogous to "cutting" the file as opposed to "copying"
     *
     * @param string $source The path to move from
     * @param string $target The path to move to
     * @return bool True if successful, otherwise false
     */
    public function move($source, $target)
    {
        return rename($source, $target);
    }

    /**
     * Reads the contents of a file
     *
     * @param string $path The path of the file whose contents we want
     * @return string The contents of the file
     * @throws FileSystemException Thrown if the path was not a valid path
     * @throws InvalidArgumentException Thrown if the path was not a string
     */
    public function read($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException("Path is not a string");
        }

        if (!$this->isFile($path)) {
            throw new FileSystemException("File at path $path not found");
        }

        return file_get_contents($path);
    }

    /**
     * Writes data to a file
     *
     * @param string $path The path to the file to write to
     * @param mixed $data The string, array, or stream source to write to the file
     * @param int $flags The bitwise-OR'd flags to use (identical to PHP's file_put_contents() flags)
     * @return int The number of bytes written
     * @throws FileSystemException Thrown if there was a problem writing to the file
     */
    public function write($path, $data, $flags = 0)
    {
        $bytesWritten = file_put_contents($path, $data, $flags);

        if ($bytesWritten === false) {
            throw new FileSystemException("Failed to write data to path $path");
        }

        return $bytesWritten;
    }
} 