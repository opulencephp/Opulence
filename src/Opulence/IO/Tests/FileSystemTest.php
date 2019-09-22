<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\IO\Tests;

use DateTime;
use Opulence\IO\FileSystem;
use Opulence\IO\FileSystemException;

/**
 * Tests the file system
 */
class FileSystemTest extends \PHPUnit\Framework\TestCase
{
    private FileSystem $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem();
    }

    protected function tearDown(): void
    {
        @unlink(__DIR__ . '/test.txt');
    }

    public function testAppending(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->fileSystem->append(__DIR__ . '/test.txt', ' bar');
        $this->assertEquals('foo bar', $this->fileSystem->read(__DIR__ . '/test.txt'));
    }

    public function testCopyingDirectories(): void
    {
        $this->fileSystem->copyDirectory(__DIR__ . '/files/subdirectory', __DIR__ . '/tmp');
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/tmp/foo.txt'));
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/tmp/subdirectory/bar.txt'));
        @unlink(__DIR__ . '/tmp/subdirectory/bar.txt');
        @rmdir(__DIR__ . '/tmp/subdirectory');
        @unlink(__DIR__ . '/tmp/foo.txt');
        @rmdir(__DIR__ . '/tmp');
    }

    public function testCopyingDirectoryThatIsNotADirectory(): void
    {
        $this->assertFalse($this->fileSystem->copyDirectory(__DIR__ . '/doesnotexist', __DIR__ . '/tmp'));
    }

    public function testCopyingFile(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->copyFile(__DIR__ . '/test.txt', __DIR__ . '/test2.txt'));
        $this->assertEquals('foo', $this->fileSystem->read(__DIR__ . '/test2.txt'));
        @unlink(__DIR__ . '/test2.txt');
    }

    public function testDeletingDirectory(): void
    {
        @mkdir(__DIR__ . '/tmp');
        @mkdir(__DIR__ . '/tmp/subdirectory');
        file_put_contents(__DIR__ . '/tmp/subdirectory/foo.txt', 'bar');
        $this->fileSystem->deleteDirectory(__DIR__ . '/tmp');
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/tmp'));
        // Just in case, remove the structure
        @unlink(__DIR__ . '/tmp/subdirectory/foo.txt');
        @rmdir(__DIR__ . '/tmp/subdirectory');
        @rmdir(__DIR__ . '/tmp');
    }

    public function testDeletingFile(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->deleteFile(__DIR__ . '/test.txt'));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/test.txt'));
    }

    /**
     * Tests if a file/directory exists
     */
    public function testExists(): void
    {
        $this->assertTrue($this->fileSystem->exists(__DIR__));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/doesnotexist'));
        $this->assertTrue($this->fileSystem->exists(__FILE__));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/doesnotexist.txt'));
    }

    public function testGettingBasename(): void
    {
        $this->assertEquals('foo.txt', $this->fileSystem->getBasename(__DIR__ . '/files/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getBasename(__DIR__ . '/doesnotexist.txt');
    }

    public function testGettingDirectoriesWithRecursion(): void
    {
        $this->assertEquals([
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'subdirectory',
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'subdirectory'
        ], $this->fileSystem->getDirectories(__DIR__ . DIRECTORY_SEPARATOR . 'files', true));
    }

    public function testGettingDirectoriesWithoutRecursion(): void
    {
        $this->assertEquals(
            [__DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'subdirectory'],
            $this->fileSystem->getDirectories(__DIR__ . DIRECTORY_SEPARATOR . 'files')
        );
    }

    public function testGettingDirectoryName(): void
    {
        $this->assertEquals(
            __DIR__ . '/files/subdirectory',
            $this->fileSystem->getDirectoryName(__DIR__ . '/files/subdirectory/foo.txt')
        );
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getDirectoryName(__DIR__ . '/doesnotexist.txt');
    }

    public function testGettingExtension(): void
    {
        $this->assertEquals('txt', $this->fileSystem->getExtension(__DIR__ . '/files/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getExtension(__DIR__ . '/doesnotexist.txt');
    }

    public function testGettingFileName(): void
    {
        $this->assertEquals('foo', $this->fileSystem->getFileName(__DIR__ . '/files/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getFileName(__DIR__ . '/doesnotexist.txt');
    }

    public function testGettingFileSize(): void
    {
        $path = __DIR__ . '/files/subdirectory/foo.txt';
        $this->assertEquals(filesize($path), $this->fileSystem->getFileSize($path));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getFileSize(__DIR__ . '/doesnotexist.txt');
    }

    public function testGettingFilesWithGlob(): void
    {
        $this->assertCount(0, array_diff([
                __DIR__ . '/files/subdirectory/foo.txt'
            ], $this->fileSystem->glob(__DIR__ . '/files/subdirectory/*.txt')));
    }

    public function testGettingFilesWithInvalidPath(): void
    {
        $this->assertEquals([], $this->fileSystem->getFiles(__FILE__));
    }

    public function testGettingFilesWithRecursion(): void
    {
        $this->assertCount(0, array_diff([
                __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'bar.txt',
                __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'foo.txt',
                __FILE__
            ], $this->fileSystem->getFiles(__DIR__, true)));
    }

    public function testGettingFilesWithoutRecursion(): void
    {
        $this->assertEquals([__FILE__], $this->fileSystem->getFiles(__DIR__));
    }

    public function testGettingLastModifiedTime(): void
    {
        $path = __DIR__ . '/files/subdirectory/foo.txt';
        $this->assertEquals(
            DateTime::createFromFormat('U', (string)filemtime($path)),
            $this->fileSystem->getLastModified($path)
        );
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getLastModified(__DIR__ . '/doesnotexist.txt');
    }

    public function testInvalidDirectoryIsDirectory(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertFalse($this->fileSystem->isDirectory(__DIR__ . '/test.txt'));
    }

    public function testInvalidFileIsFile(): void
    {
        $this->assertFalse($this->fileSystem->isFile(__DIR__));
    }

    public function testIsReadable(): void
    {
        $this->assertFalse($this->fileSystem->isReadable(__DIR__ . '/doesnotexist.txt'));
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->isReadable(__DIR__ . '/test.txt'));
    }

    public function testIsWritable(): void
    {
        $this->assertFalse($this->fileSystem->isWritable(__DIR__ . '/doesnotexist.txt'));
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->isWritable(__DIR__ . '/test.txt'));
    }

    public function testMakingDirectory(): void
    {
        $this->assertTrue($this->fileSystem->makeDirectory(__DIR__ . '/tmp'));
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/tmp'));
        rmdir(__DIR__ . '/tmp');
    }

    public function testMovingFile(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo bar');
        $this->assertTrue($this->fileSystem->move(__DIR__ . '/test.txt', __DIR__ . '/test2.txt'));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/test.txt'));
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/test2.txt'));
        $this->assertEquals('foo bar', $this->fileSystem->read(__DIR__ . '/test2.txt'));
        @unlink(__DIR__ . '/test2.txt');
    }

    public function testReadingFileThatDoesNotExist(): void
    {
        $this->expectException(FileSystemException::class);
        $this->assertEquals('foo', $this->fileSystem->read(__DIR__ . '/doesnotexist.txt'));
    }

    public function testReadingFileThatExists(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertEquals('foo', $this->fileSystem->read(__DIR__ . '/test.txt'));
    }

    public function testValidDirectoryIsDirectory(): void
    {
        $this->assertTrue($this->fileSystem->isDirectory(__DIR__));
    }

    public function testValidFileIsFile(): void
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->isFile(__DIR__ . '/test.txt'));
    }

    public function testWritingToFile(): void
    {
        $this->assertTrue(is_numeric($this->fileSystem->write(__DIR__ . '/test.txt', 'foo bar')));
        $this->assertEquals('foo bar', $this->fileSystem->read(__DIR__ . '/test.txt'));
    }
}
