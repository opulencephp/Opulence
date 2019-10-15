<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cache\tests;

use Opulence\Cache\FileBridge;
use PHPUnit\Framework\TestCase;

/**
 * Tests the file bridge
 */
class FileBridgeTest extends TestCase
{
    private FileBridge $bridge;

    public static function setUpBeforeClass(): void
    {
        if (!is_dir(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    public static function tearDownAfterClass(): void
    {
        $files = glob(__DIR__ . '/tmp/*');

        foreach ($files as $file) {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        rmdir(__DIR__ . '/tmp');
    }

    protected function setUp(): void
    {
        $this->bridge = new FileBridge(__DIR__ . '/tmp');
    }

    public function testCheckingIfKeyExists(): void
    {
        $this->assertFalse($this->bridge->has('foo'));
        // Try a null value
        $this->bridge->set('foo', null, 60);
        $this->assertTrue($this->bridge->has('foo'));
        // Try an actual value
        $this->bridge->set('foo', 'bar', 60);
        $this->assertTrue($this->bridge->has('foo'));
    }

    public function testDecrementingValues(): void
    {
        $this->bridge->set('foo', 11, 60);
        // Test using default value
        $this->assertEquals(10, $this->bridge->decrement('foo'));
        // Test using a custom value
        $this->assertEquals(5, $this->bridge->decrement('foo', 5));
    }

    public function testDeletingKey(): void
    {
        $this->bridge->set('foo', 'bar', 60);
        $this->bridge->delete('foo');
        $this->assertFalse($this->bridge->has('foo'));
    }

    public function testExpiredKeyIsNotRead(): void
    {
        $this->bridge->set('foo', 'bar', -1);
        $this->assertFalse($this->bridge->has('foo'));
        $this->assertNull($this->bridge->get('foo'));
    }

    public function testFlushing(): void
    {
        $this->bridge->set('foo', 'bar', 60);
        $this->bridge->set('baz', 'blah', 60);
        $this->bridge->flush();
        $this->assertFalse($this->bridge->has('foo'));
        $this->assertFalse($this->bridge->has('baz'));
    }

    /**
     * Tests getting a non-existent key
     */
    public function testGettingNonExistentKey(): void
    {
        $this->assertNull($this->bridge->get('foo'));
    }

    public function testGettingSetValue(): void
    {
        $this->bridge->set('foo', 'bar', 60);
        $this->assertEquals('bar', $this->bridge->get('foo'));
    }

    public function testIncrementingValues(): void
    {
        $this->bridge->set('foo', 1, 60);
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment('foo'));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment('foo', 5));
    }

    public function testTrailingSlashGetsTrimmed(): void
    {
        $bridge = new FileBridge(__DIR__ . '/tmp/');
        $bridge->set('foo', 'bar', 60);
        $this->assertFileExists(__DIR__ . '/tmp/' . md5('foo'));
        $this->assertEquals('bar', $bridge->get('foo'));
    }
}
