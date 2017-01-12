<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cache;

/**
 * Tests that array bridge
 */
class ArrayBridgeTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayBridge The bridge to use in tests */
    private $bridge = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->bridge = new ArrayBridge();
    }

    /**
     * Tests checking if a key exists
     */
    public function testCheckingIfKeyExists()
    {
        $this->assertFalse($this->bridge->has('foo'));
        // Try a null value
        $this->bridge->set('foo', null, 60);
        $this->assertTrue($this->bridge->has('foo'));
        // Try an actual value
        $this->bridge->set('foo', 'bar', 60);
        $this->assertTrue($this->bridge->has('foo'));
    }

    /**
     * Tests decrementing values
     */
    public function testDecrementingValues()
    {
        $this->bridge->set('foo', 11, 60);
        // Test using default value
        $this->assertEquals(10, $this->bridge->decrement('foo'));
        // Test using a custom value
        $this->assertEquals(5, $this->bridge->decrement('foo', 5));
    }

    /**
     * Tests deleting a key
     */
    public function testDeletingKey()
    {
        $this->bridge->set('foo', 'bar', 60);
        $this->bridge->delete('foo');
        $this->assertFalse($this->bridge->has('foo'));
    }

    /**
     * Tests flushing
     */
    public function testFlushing()
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
    public function testGettingNonExistentKey()
    {
        $this->assertNull($this->bridge->get('foo'));
    }

    /**
     * Tests getting a set value
     */
    public function testGettingSetValue()
    {
        $this->bridge->set('foo', 'bar', 60);
        $this->assertEquals('bar', $this->bridge->get('foo'));
    }

    /**
     * Tests incrementing values
     */
    public function testIncrementingValues()
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
    public function testSettingValueWithNegativeLifetime()
    {
        $this->bridge->set('foo', 'bar', 0);
        $this->assertFalse($this->bridge->has('foo'));
        $this->bridge->set('foo', 'bar', -1);
        $this->assertFalse($this->bridge->has('foo'));
    }
}
