<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests that array bridge
 */
namespace RDev\Cache;

class ArrayBridgeTest extends \PHPUnit_Framework_TestCase
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
        $this->assertFalse($this->bridge->has("foo"));
        // Try a null value
        $this->bridge->set("foo", null);
        $this->assertTrue($this->bridge->has("foo"));
        // Try an actual value
        $this->bridge->set("foo", "bar");
        $this->assertTrue($this->bridge->has("foo"));
    }

    /**
     * Tests decrementing values
     */
    public function testDecrementingValues()
    {
        $this->bridge->set("foo", 11);
        // Test using default value
        $this->assertEquals(10, $this->bridge->decrement("foo"));
        // Test using a custom value
        $this->assertEquals(5, $this->bridge->decrement("foo", 5));
    }

    /**
     * Tests deleting a key
     */
    public function testDeletingKey()
    {
        $this->bridge->set("foo", "bar");
        $this->bridge->delete("foo");
        $this->assertFalse($this->bridge->has("foo"));
    }

    /**
     * Tests that the driver is null
     */
    public function testDriverIsNull()
    {
        $this->assertNull($this->bridge->getDriver());
    }

    /**
     * Tests flushing
     */
    public function testFlushing()
    {
        $this->bridge->set("foo", "bar");
        $this->bridge->set("baz", "blah");
        $this->bridge->flush();
        $this->assertFalse($this->bridge->has("foo"));
        $this->assertFalse($this->bridge->has("baz"));
    }

    /**
     * Tests getting a non-existent key
     */
    public function testGettingNonExistentKey()
    {
        $this->assertNull($this->bridge->get("foo"));
    }

    /**
     * Tests getting a set value
     */
    public function testGettingSetValue()
    {
        $this->bridge->set("foo", "bar");
        $this->assertEquals("bar", $this->bridge->get("foo"));
    }

    /**
     * Tests incrementing values
     */
    public function testIncrementingValues()
    {
        $this->bridge->set("foo", 1);
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment("foo"));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment("foo", 5));
    }
}