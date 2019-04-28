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
    private static $path = 'tmp';
    /** @var FileSessionHandler The handler to test */
    private $handler;
    /** @var FileSystem The file system to use in tests */
    private $fileSystem;

    /**
     * Does some setup before any tests
     */
    public static function setUpBeforeClass(): void
    {
        if (!is_dir(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    /**
     * Performs some garbage collection
     */
    public static function tearDownAfterClass(): void
    {
        $files = glob(__DIR__ . '/' . self::$path . '/*');

        foreach ($files as $file) {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        rmdir(__DIR__ . '/' . self::$path);
    }

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem();
        $this->handler = new FileSessionHandler(__DIR__ . '/' . self::$path);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    protected function tearDown(): void
    {
        @unlink(__DIR__ . '/' . self::$path . '/foo');
        @unlink(__DIR__ . '/' . self::$path . '/bar');
    }

    /**
     * Tests the close function
     */
    public function testClose(): void
    {
        $this->assertTrue($this->handler->close());
    }

    /**
     * Tests garbage collection
     */
    public function testGarbageCollection(): void
    {
        $this->handler->write('foo', 'bar');
        $this->handler->write('bar', 'baz');
        $this->handler->gc(-1);
        $this->assertEquals([], $this->fileSystem->glob(__DIR__ . '/' . self::$path . '/*'));
    }

    /**
     * Tests the open function
     */
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

    /**
     * Tests reading a session
     */
    public function testReadingSession(): void
    {
        $this->fileSystem->write(__DIR__ . '/' . self::$path . '/foo', 'bar');
        $this->assertEquals('bar', $this->handler->read('foo'));
    }

    /**
     * Tests writing a session
     */
    public function testWritingSession(): void
    {
        $this->handler->write('foo', 'bar');
        $this->assertEquals('bar', $this->fileSystem->read(__DIR__ . '/' . self::$path . '/foo'));
    }
}
