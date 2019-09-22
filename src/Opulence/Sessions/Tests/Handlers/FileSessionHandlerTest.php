<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Sessions\Tests\Handlers;

use Opulence\IO\FileSystem;
use Opulence\Sessions\Handlers\FileSessionHandler;

/**
 * Test the file session handler
 */
class FileSessionHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var string The path to the temporary session files */
    private static string $path = 'tmp';
    private FileSessionHandler $handler;
    private FileSystem $fileSystem;

    public static function setUpBeforeClass(): void
    {
        if (!is_dir(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    public static function tearDownAfterClass(): void
    {
        $files = glob(__DIR__ . '/' . self::$path . '/*');

        foreach ($files as $file) {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        rmdir(__DIR__ . '/' . self::$path);
    }

    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem();
        $this->handler = new FileSessionHandler(__DIR__ . '/' . self::$path);
    }

    protected function tearDown(): void
    {
        @unlink(__DIR__ . '/' . self::$path . '/foo');
        @unlink(__DIR__ . '/' . self::$path . '/bar');
    }

    public function testClose(): void
    {
        $this->assertTrue($this->handler->close());
    }

    public function testGarbageCollection(): void
    {
        $this->handler->write('foo', 'bar');
        $this->handler->write('bar', 'baz');
        $this->handler->gc(-1);
        $this->assertEquals([], $this->fileSystem->glob(__DIR__ . '/' . self::$path . '/*'));
    }

    public function testOpen(): void
    {
        $this->assertTrue($this->handler->open(__DIR__ . '/' . self::$path . '/foo', '123'));
    }

    /**
     * Tests reading a non-existent session
     */
    public function testReadingNonExistentSession(): void
    {
        $this->assertEmpty($this->handler->read('non-existent'));
    }

    public function testReadingSession(): void
    {
        $this->fileSystem->write(__DIR__ . '/' . self::$path . '/foo', 'bar');
        $this->assertEquals('bar', $this->handler->read('foo'));
    }

    public function testWritingSession(): void
    {
        $this->handler->write('foo', 'bar');
        $this->assertEquals('bar', $this->fileSystem->read(__DIR__ . '/' . self::$path . '/foo'));
    }
}
