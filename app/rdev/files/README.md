# File System

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
  1. [Reading a File](#reading-a-file)
  2. [Writing to a File](#writing-to-a-file)
  3. [Appending to a File](#appending-to-a-file)
  4. [Deleting a File](#deleting-a-file)
  5. [Checking if Something is a File](#checking-if-something-is-a-file)
  6. [Checking if a File is Readable](#checking-if-a-file-is-readable)
  7. [Checking if a File is Writable](#checking-if-a-file-is-writable)
  8. [Copying a File](#copying-a-file)
  9. [Moving a File](#moving-a-file)
  10. [Getting a File's Directory Name](#getting-a-files-directory-name)
  11. [Getting a File's Basename](#getting-a-file-sbasename)
  12. [Getting a File's Name](#getting-a-files-name)
  13. [Getting a File's Extension](#getting-a-files-extension)
  14. [Getting a File's Size](#getting-a-files-size)
  15. [Getting a File's Last Modified Time](#getting-a-files-last-modified-time)
  16. [Getting Files in a Directory](#getting-files-in-a-directory)
  17. [Checking if a File or Directory Exists](#checking-if-a-file-or-directory-exists)
  18. [Creating a Directory](#creating-a-directory)
  19. [Deleting a Directory](#deleting-a-directory)
  20. [Getting the List of Directories](#getting-the-list-of-directories)
  21. [Copying a Directory](#copying-a-directory)

## Introduction
Most programs interact with a computer's file system in some way.  RDev comes with the `FileSystem` class to facilitate these interactions.  With it, you can easily read and write files, get attributes of files, copy files and folders, and recursively delete directories, and do other common tasks.

## Basic Usage
For all examples below, assume `$fileSystem = new \RDev\Files\FileSystem();`.

#### Reading a File
```php
$fileSystem->read(FILE_PATH);
```

#### Writing to a File
```php
// The third parameter is identical to PHP's file_put_contents() flags
$fileSystem->write(FILE_PATH, "foo", \LOCK_EX);
```

#### Appending to a File
```php
$fileSystem->append(FILE_PATH, "foo");
```

#### Deleting a File
```php
$fileSystem->deleteFile(FILE_PATH);
```

#### Checking if Something is a File
```php
$fileSystem->isFile(FILE_PATH);
```

#### Checking if a File is Readable
```php
$fileSystem->isReadable(FILE_PATH);
```

#### Checking if a File is Writable
```php
$fileSystem->isWritable(FILE_PATH);
```

#### Copying a File
```php
$fileSystem->copy(SOURCE_FILE, TARGET_PATH);
```

#### Moving a File
```php
// This is analogous to "cutting" the file
$fileSystem->move(SOURCE_FILE, TARGET_PATH);
```

#### Getting a File's Directory Name
```php
$fileSystem->getDirectoryName(FILE_PATH);
```

#### Getting a File's Basename
```php
// This returns everything in the file name except for the path preceding it
$fileSystem->getBaseName(FILE_PATH);
```

#### Getting a File's Name
```php
// This returns the file name without the extension
$fileSystem->getFileName(FILE_PATH);
```

#### Getting a File's Extension
```php
$fileSystem->getExtension(FILE_PATH);
```

#### Getting a File's Size
```php
// The size of the file in bytes
$fileSystem->getFileSize(FILE_PATH);
```

#### Getting a File's Last Modified Time
```php
$fileSystem->getLastModified(FILE_PATH);
```

#### Getting Files in a Directory
```php
// The second parameter determines whether or not we recurse into subdirectories
// This returns the full path of all the files found
$fileSystem->getFiles(DIRECTORY_PATH, true);
```

#### Checking if a File or Directory Exists
```php
$fileSystem->exists(FILE_PATH);
```

#### Creating a Directory
```php
// The second parameter is the chmod permissions
// The third parameter determines whether or not we create nested subdirectories
$fileSystem->createDirectory(DIRECTORY_PATH, 0777, true);
```

#### Deleting a Directory
```php
// The second parameter determines whether or not we keep the directory structure
$fileSystem->deleteDirectory(DIRECTORY_PATH, false);
```

#### Getting the List of Directories
```php
// The second parameter determines whether or not we recurse into the directories
// This returns the full path of all the directories found
$fileSystem->getDirectories(DIRECTORY_PATH, true);
```

#### Copying a Directory
```php
$fileSystem->copyDirectory(SOURCE_DIRECTORY, TARGET_PATH);
```