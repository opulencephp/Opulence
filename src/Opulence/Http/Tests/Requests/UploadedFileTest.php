<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests\Requests;

use Opulence\Http\Requests\UploadException;
use Opulence\Http\Tests\Requests\Mocks\UploadedFile as MockUploadedFile;

/**
 * Tests the uploaded file
 */
class UploadedFileTest extends \PHPUnit\Framework\TestCase
{
    /** The uploaded file's filename */
    const UPLOADED_FILE_FILENAME = '/files/UploadedFile.txt';
    /** The temporary file's filename */
    const TEMP_FILENAME = '/files/TempFile.txt';
    /** @var MockUploadedFile The uploaded file to use in tests */
    private $file = null;

    /**
     * Tears down the class
     */
    public static function tearDownAfterClass()
    {
        $files = glob(__DIR__ . '/tmp/*');

        foreach ($files as $file) {
            unlink($file);
        }

        if (file_exists(__DIR__ . '/tmp')) {
            rmdir(__DIR__ . '/tmp');
        }
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->file = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100,
            'text/plain',
            UPLOAD_ERR_OK
        );
    }

    /**
     * Tests checking for errors
     */
    public function testCheckingForErrors()
    {
        $validFile = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100,
            'text/plain',
            UPLOAD_ERR_OK
        );
        $this->assertFalse($validFile->hasErrors());
        $invalidFile = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100,
            'text/plain',
            UPLOAD_ERR_EXTENSION
        );
        $this->assertTrue($invalidFile->hasErrors());
    }

    /**
     * Tests getting the default error
     */
    public function testGettingDefaultError()
    {
        $file = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100
        );
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
    }

    /**
     * Tests getting the default temp mime type
     */
    public function testGettingDefaultTempMimeType()
    {
        $file = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100
        );
        $this->assertEmpty($file->getTempMimeType());
    }

    /**
     * Tests getting the error
     */
    public function testGettingError()
    {
        $file = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100,
            'text/plain',
            UPLOAD_ERR_EXTENSION
        );
        $this->assertEquals(UPLOAD_ERR_EXTENSION, $file->getError());
    }

    /**
     * Tests getting the mime type
     */
    public function testGettingMimeType()
    {
        $file = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100,
            'foo/bar'
        );
        $this->assertEquals('text/plain', $file->getMimeType());
    }

    /**
     * Tests getting the path
     */
    public function testGettingPath()
    {
        $this->assertEquals(__DIR__ . '/files', $this->file->getPath());
    }

    /**
     * Tests getting the temp extension
     */
    public function testGettingTempExtension()
    {
        $this->assertEquals('txt', $this->file->getTempExtension());
    }

    /**
     * Tests getting the temp filename
     */
    public function testGettingTempFilename()
    {
        $this->assertEquals(__DIR__ . self::TEMP_FILENAME, $this->file->getTempFilename());
    }

    /**
     * Tests getting the temp mime type
     */
    public function testGettingTempMimeType()
    {
        $this->assertEquals('text/plain', $this->file->getTempMimeType());
    }

    /**
     * Tests getting the size
     */
    public function testGettingTempSize()
    {
        $this->assertEquals(100, $this->file->getTempSize());
    }

    /**
     * Tests moving the file
     */
    public function testMovingFile()
    {
        // Test specifying directory for target and a filename
        $this->file->move(__DIR__ . '/tmp', 'bar.txt');
        $this->assertEquals('bar', file_get_contents(__DIR__ . '/tmp/bar.txt'));
        // Test not specifying a name
        $this->file->move(__DIR__ . '/tmp');
        $this->assertEquals('bar', file_get_contents(__DIR__ . '/tmp/UploadedFile.txt'));
    }

    /**
     * Tests moving a file with errors
     */
    public function testMovingFileWithErrors()
    {
        $this->expectException(UploadException::class);
        $file = new MockUploadedFile(
            __DIR__ . self::UPLOADED_FILE_FILENAME,
            __DIR__ . self::TEMP_FILENAME,
            100,
            'text/plain',
            UPLOAD_ERR_EXTENSION
        );
        $file->move(__DIR__ . '/tmp', 'foo.txt');
    }
}
