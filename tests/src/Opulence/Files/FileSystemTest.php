<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Files;

use DateTime;

/**
 * Tests the file system
 */
class FileSystemTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileSystem The file system to use in tests */
    private $fileSystem = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new FileSystem();
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        @unlink(__DIR__ . '/test.txt');
    }

    /**
     * Tests appending to a file
     */
    public function testAppending()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->fileSystem->append(__DIR__ . '/test.txt', ' bar');
        $this->assertEquals('foo bar', $this->fileSystem->read(__DIR__ . '/test.txt'));
    }

    /**
     * Tests copying directories
     */
    public function testCopyingDirectories()
    {
        $this->fileSystem->copyDirectory(__DIR__ . '/subdirectory', __DIR__ . '/tmp');
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/tmp/foo.txt'));
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/tmp/subdirectory/bar.txt'));
        @unlink(__DIR__ . '/tmp/subdirectory/bar.txt');
        @rmdir(__DIR__ . '/tmp/subdirectory');
        @unlink(__DIR__ . '/tmp/foo.txt');
        @rmdir(__DIR__ . '/tmp');
    }

    /**
     * Tests copying a directory that is not a directory
     */
    public function testCopyingDirectoryThatIsNotADirectory()
    {
        $this->assertFalse($this->fileSystem->copyDirectory(__DIR__ . '/doesnotexist', __DIR__ . '/tmp'));
    }

    /**
     * Tests copying a file
     */
    public function testCopyingFile()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->copyFile(__DIR__ . '/test.txt', __DIR__ . '/test2.txt'));
        $this->assertEquals('foo', $this->fileSystem->read(__DIR__ . '/test2.txt'));
        @unlink(__DIR__ . '/test2.txt');
    }

    /**
     * Tests deleting a directory
     */
    public function testDeletingDirectory()
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

    /**
     * Tests deleting a file
     */
    public function testDeletingFile()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->deleteFile(__DIR__ . '/test.txt'));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/test.txt'));
    }

    /**
     * Tests if a file/directory exists
     */
    public function testExists()
    {
        $this->assertTrue($this->fileSystem->exists(__DIR__));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/doesnotexist'));
        $this->assertTrue($this->fileSystem->exists(__FILE__));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/doesnotexist.txt'));
    }

    /**
     * Tests getting the basename
     */
    public function testGettingBasename()
    {
        $this->assertEquals('foo.txt', $this->fileSystem->getBasename(__DIR__ . '/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getBasename(__DIR__ . '/doesnotexist.txt');
    }

    /**
     * Tests getting the directories with recursion
     */
    public function testGettingDirectoriesWithRecursion()
    {
        $this->assertEquals([
            __DIR__ . DIRECTORY_SEPARATOR . 'subdirectory',
            __DIR__ . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'subdirectory'
        ], $this->fileSystem->getDirectories(__DIR__, true));
    }

    /**
     * Tests getting the directories without recursion
     */
    public function testGettingDirectoriesWithoutRecursion()
    {
        $this->assertEquals(
            [__DIR__ . DIRECTORY_SEPARATOR . 'subdirectory'],
            $this->fileSystem->getDirectories(__DIR__)
        );
    }

    /**
     * Tests getting the directory name
     */
    public function testGettingDirectoryName()
    {
        $this->assertEquals(__DIR__ . '/subdirectory',
            $this->fileSystem->getDirectoryName(__DIR__ . '/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getDirectoryName(__DIR__ . '/doesnotexist.txt');
    }

    /**
     * Tests getting the extension
     */
    public function testGettingExtension()
    {
        $this->assertEquals('txt', $this->fileSystem->getExtension(__DIR__ . '/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getExtension(__DIR__ . '/doesnotexist.txt');
    }

    /**
     * Tests getting the file name
     */
    public function testGettingFileName()
    {
        $this->assertEquals('foo', $this->fileSystem->getFileName(__DIR__ . '/subdirectory/foo.txt'));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getFileName(__DIR__ . '/doesnotexist.txt');
    }

    /**
     * Tests getting the file size
     */
    public function testGettingFileSize()
    {
        $path = __DIR__ . '/subdirectory/foo.txt';
        $this->assertEquals(filesize($path), $this->fileSystem->getFileSize($path));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getFileSize(__DIR__ . '/doesnotexist.txt');
    }

    /**
     * Tests getting files with glob
     */
    public function testGettingFilesWithGlob()
    {
        $this->assertTrue(count(array_diff([
                __DIR__ . '/subdirectory/foo.txt'
            ], $this->fileSystem->glob(__DIR__ . '/subdirectory/*.txt'))) == 0);
    }

    /**
     * Tests getting the files with an invalid path
     */
    public function testGettingFilesWithInvalidPath()
    {
        $this->assertEquals([], $this->fileSystem->getFiles(__FILE__));
    }

    /**
     * Tests getting the files with recursion
     */
    public function testGettingFilesWithRecursion()
    {
        $this->assertTrue(count(array_diff([
                __DIR__ . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'bar.txt',
                __DIR__ . DIRECTORY_SEPARATOR . 'subdirectory' . DIRECTORY_SEPARATOR . 'foo.txt',
                __FILE__
            ], $this->fileSystem->getFiles(__DIR__, true))) == 0);
    }

    /**
     * Tests getting the files without recursion
     */
    public function testGettingFilesWithoutRecursion()
    {
        $this->assertEquals([__FILE__], $this->fileSystem->getFiles(__DIR__));
    }

    /**
     * Tests getting the last modified time
     */
    public function testGettingLastModifiedTime()
    {
        $path = __DIR__ . '/subdirectory/foo.txt';
        $this->assertEquals(DateTime::createFromFormat('U', filemtime($path)),
            $this->fileSystem->getLastModified($path));
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getLastModified(__DIR__ . '/doesnotexist.txt');
    }

    /**
     * Tests checking if an invalid directory is not a directory
     */
    public function testInvalidDirectoryIsDirectory()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertFalse($this->fileSystem->isDirectory(__DIR__ . '/test.txt'));
    }

    /**
     * Tests checking if an invalid file is not a file
     */
    public function testInvalidFileIsFile()
    {
        $this->assertFalse($this->fileSystem->isFile(__DIR__));
    }

    /**
     * Tests if a file is readable
     */
    public function testIsReadable()
    {
        $this->assertFalse($this->fileSystem->isReadable(__DIR__ . '/doesnotexist.txt'));
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->isReadable(__DIR__ . '/test.txt'));
    }

    /**
     * Tests if a file is writable
     */
    public function testIsWritable()
    {
        $this->assertFalse($this->fileSystem->isWritable(__DIR__ . '/doesnotexist.txt'));
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->isWritable(__DIR__ . '/test.txt'));
    }

    /**
     * Tests making a directory
     */
    public function testMakingDirectory()
    {
        $this->assertTrue($this->fileSystem->makeDirectory(__DIR__ . '/tmp'));
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/tmp'));
        rmdir(__DIR__ . '/tmp');
    }

    /**
     * Tests moving a file
     */
    public function testMovingFile()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo bar');
        $this->assertTrue($this->fileSystem->move(__DIR__ . '/test.txt', __DIR__ . '/test2.txt'));
        $this->assertFalse($this->fileSystem->exists(__DIR__ . '/test.txt'));
        $this->assertTrue($this->fileSystem->exists(__DIR__ . '/test2.txt'));
        $this->assertEquals('foo bar', $this->fileSystem->read(__DIR__ . '/test2.txt'));
        @unlink(__DIR__ . '/test2.txt');
    }

    /**
     * Tests reading a file that doesn't exist
     */
    public function testReadingFileThatDoesNotExist()
    {
        $this->expectException(FileSystemException::class);
        $this->assertEquals('foo', $this->fileSystem->read(__DIR__ . '/doesnotexist.txt'));
    }

    /**
     * Tests reading a file that exists
     */
    public function testReadingFileThatExists()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertEquals('foo', $this->fileSystem->read(__DIR__ . '/test.txt'));
    }

    /**
     * Tests checking if a valid directory is a directory
     */
    public function testValidDirectoryIsDirectory()
    {
        $this->assertTrue($this->fileSystem->isDirectory(__DIR__));
    }

    /**
     * Tests checking if a valid file is a file
     */
    public function testValidFileIsFile()
    {
        file_put_contents(__DIR__ . '/test.txt', 'foo');
        $this->assertTrue($this->fileSystem->isFile(__DIR__ . '/test.txt'));
    }

    /**
     * Tests writing to a file
     */
    public function testWritingToFile()
    {
        $this->assertTrue(is_numeric($this->fileSystem->write(__DIR__ . '/test.txt', 'foo bar')));
        $this->assertEquals('foo bar', $this->fileSystem->read(__DIR__ . '/test.txt'));
    }
}
