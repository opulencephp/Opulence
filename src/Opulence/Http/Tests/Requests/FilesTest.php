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

use Opulence\Http\Requests\Files;
use Opulence\Http\Requests\UploadedFile;

/**
 * Tests the file parameters
 */
class FilesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding files creates files
     */
    public function testAddingFilesCreatesFiles(): void
    {
        $files = new Files([]);
        $files->add('foo', [
            'tmp_name' => '/path/foo.txt',
            'name' => 'foo.txt',
            'type' => 'text/plain',
            'size' => 100,
            'error' => UPLOAD_ERR_EXTENSION
        ]);
        /** @var UploadedFile $file */
        $file = $files->get('foo');
        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals('/path', $file->getPath());
        $this->assertEquals('foo.txt', $file->getTempFilename());
        $this->assertEquals(100, $file->getTempSize());
        $this->assertEquals('text/plain', $file->getTempMimeType());
        $this->assertEquals(UPLOAD_ERR_EXTENSION, $file->getError());
    }

    /**
     * Tests passing files through constructor creates files
     */
    public function testPassingFilesThroughConstructorCreatesFiles(): void
    {
        $files = new Files([
            'foo' => [
                'tmp_name' => '/path/foo.txt',
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'size' => 100,
                'error' => UPLOAD_ERR_EXTENSION
            ]
        ]);
        /** @var UploadedFile $file */
        $file = $files->get('foo');
        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals('/path', $file->getPath());
        $this->assertEquals('foo.txt', $file->getTempFilename());
        $this->assertEquals(100, $file->getTempSize());
        $this->assertEquals('text/plain', $file->getTempMimeType());
        $this->assertEquals(UPLOAD_ERR_EXTENSION, $file->getError());
    }
}
