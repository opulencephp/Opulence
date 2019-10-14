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

use Opulence\Cache\ArrayBridge;

/**
 * Tests that array bridge
 */
class ArrayBridgeTest extends \PHPUnit\Framework\TestCase
{
    private ArrayBridge $bridge;

    protected function setUp(): void
    {
        $this->bridge = new ArrayBridge();
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

    /**
     * Tests that setting a value with a lifetime of 0 or lower is not retained in cache
     */
    public function testSettingValueWithNegativeLifetime(): void
    {
        $this->bridge->set('foo', 'bar', 0);
        $this->assertFalse($this->bridge->has('foo'));
        $this->bridge->set('foo', 'bar', -1);
        $this->assertFalse($this->bridge->has('foo'));
    }
}
