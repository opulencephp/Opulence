<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http\Tests\Requests;

use Opulence\Http\Requests\UploadException;
use Opulence\Http\Tests\Requests\Mocks\UploadedFile as MockUploadedFile;

/**
 * Tests the uploaded file
 */
class UploadedFileTest extends \PHPUnit\Framework\TestCase
{
    /** The uploaded file's filename */
    private const UPLOADED_FILE_FILENAME = '/files/UploadedFile.txt';
    /** The temporary file's filename */
    private const TEMP_FILENAME = '/files/TempFile.txt';
    /** @var MockUploadedFile The uploaded file to use in tests */
    private $file;

    /**
     * Tears down the class
     */
    public static function tearDownAfterClass(): void
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
    protected function setUp(): void
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
    public function testCheckingForErrors(): void
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
    public function testGettingDefaultError(): void
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
    public function testGettingDefaultTempMimeType(): void
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
    public function testGettingError(): void
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
    public function testGettingMimeType(): void
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
    public function testGettingPath(): void
    {
        $this->assertEquals(__DIR__ . '/files', $this->file->getPath());
    }

    /**
     * Tests getting the temp extension
     */
    public function testGettingTempExtension(): void
    {
        $this->assertEquals('txt', $this->file->getTempExtension());
    }

    /**
     * Tests getting the temp filename
     */
    public function testGettingTempFilename(): void
    {
        $this->assertEquals(__DIR__ . self::TEMP_FILENAME, $this->file->getTempFilename());
    }

    /**
     * Tests getting the temp mime type
     */
    public function testGettingTempMimeType(): void
    {
        $this->assertEquals('text/plain', $this->file->getTempMimeType());
    }

    /**
     * Tests getting the size
     */
    public function testGettingTempSize(): void
    {
        $this->assertEquals(100, $this->file->getTempSize());
    }

    /**
     * Tests moving the file
     */
    public function testMovingFile(): void
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
    public function testMovingFileWithErrors(): void
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
