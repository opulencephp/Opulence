<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Requests;

/**
 * Tests the file parameters
 */
class FilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding files creates files
     */
    public function testAddingFilesCreatesFiles()
    {
        $files = new Files([]);
        $files->add("foo", [
            "tmp_name" => "/path/foo.txt",
            "name" => "foo.txt",
            "type" => "text/plain",
            "size" => 100,
            "error" => UPLOAD_ERR_EXTENSION
        ]);
        /** @var UploadedFile $file */
        $file = $files->get("foo");
        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals("/path", $file->getPath());
        $this->assertEquals("foo.txt", $file->getTempFilename());
        $this->assertEquals(100, $file->getTempSize());
        $this->assertEquals("text/plain", $file->getTempMimeType());
        $this->assertEquals(UPLOAD_ERR_EXTENSION, $file->getError());
    }

    /**
     * Tests passing files through constructor creates files
     */
    public function testPassingFilesThroughConstructorCreatesFiles()
    {
        $files = new Files([
            "foo" => [
                "tmp_name" => "/path/foo.txt",
                "name" => "foo.txt",
                "type" => "text/plain",
                "size" => 100,
                "error" => UPLOAD_ERR_EXTENSION
            ]
        ]);
        /** @var UploadedFile $file */
        $file = $files->get("foo");
        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals("/path", $file->getPath());
        $this->assertEquals("foo.txt", $file->getTempFilename());
        $this->assertEquals(100, $file->getTempSize());
        $this->assertEquals("text/plain", $file->getTempMimeType());
        $this->assertEquals(UPLOAD_ERR_EXTENSION, $file->getError());
    }
}