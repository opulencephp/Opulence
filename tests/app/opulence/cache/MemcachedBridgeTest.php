<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Memcached bridge
 */
namespace Opulence\Cache;
use Memcached;
use Opulence\Memcached\TypeMapper;
use Opulence\Tests\Memcached\Mocks\OpulenceMemcached;

class MemcachedBridgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var MemcachedBridge The bridge to use in tests */
    private $bridge = null;
    /** @var OpulenceMemcached The Memcached driver */
    private $memcached = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $methodsToMock = ["decrement", "delete", "flush", "get", "getResultCode", "increment", "set"];
        $constructorParams = [$this->getMock(TypeMapper::class)];
        $this->memcached = $this->getMock(OpulenceMemcached::class, $methodsToMock, $constructorParams);
        $this->bridge = new MemcachedBridge($this->memcached, "dave:");
    }

    /**
     * Tests checking if a key exists
     */
    public function testCheckingIfKeyExists()
    {
        $this->memcached->expects($this->at(0))->method("get")->will($this->returnValue(false));
        $this->memcached->expects($this->at(1))->method("get")->will($this->returnValue("bar"));
        $this->assertFalse($this->bridge->has("foo"));
        $this->assertTrue($this->bridge->has("foo"));
    }

    /**
     * Tests decrementing returns correct values
     */
    public function testDecrementingReturnsCorrectValues()
    {
        $this->memcached->expects($this->at(0))->method("decrement")->with("dave:foo", 1)->will($this->returnValue(10));
        $this->memcached->expects($this->at(1))->method("decrement")->with("dave:foo", 5)->will($this->returnValue(5));
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
        $this->memcached->expects($this->once())->method("delete")->with("dave:foo");
        $this->bridge->delete("foo");
    }

    /**
     * Tests that the driver is the correct instance of Memcached
     */
    public function testDriverIsCorrectInstance()
    {
        $this->assertSame($this->memcached, $this->bridge->getMemcached());
    }

    /**
     * Tests that an error when getting a value will return null
     */
    public function testErrorDuringGetWillReturnNull()
    {
        $this->memcached->expects($this->once())->method("get")->will($this->returnValue("bar"));
        $this->memcached->expects($this->once())->method("getResultCode")->will($this->returnValue(1));
        $this->assertNull($this->bridge->get("foo"));
    }

    /**
     * Tests flushing the database
     */
    public function testFlushing()
    {
        $this->memcached->expects($this->once())->method("flush");
        $this->bridge->flush();
    }

    /**
     * Tests that getting a value works
     */
    public function testGetWorks()
    {
        $this->memcached->expects($this->once())->method("get")->will($this->returnValue("bar"));
        $this->memcached->expects($this->once())->method("getResultCode")->will($this->returnValue(0));
        $this->assertEquals("bar", $this->bridge->get("foo"));
    }

    /**
     * Tests incrementing returns correct values
     */
    public function testIncrementingReturnsCorrectValues()
    {
        $this->memcached->expects($this->at(0))->method("increment")->with("dave:foo", 1)->will($this->returnValue(2));
        $this->memcached->expects($this->at(1))->method("increment")->with("dave:foo", 5)->will($this->returnValue(7));
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment("foo"));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment("foo", 5));
    }

    /**
     * Tests that null is returned on cache miss
     */
    public function testNullIsReturnedOnMiss()
    {
        $this->memcached->expects($this->once())->method("get")->will($this->returnValue(false));
        $this->assertNull($this->bridge->get("foo"));
    }

    /**
     * Tests setting a value
     */
    public function testSettingValue()
    {
        $this->memcached->expects($this->once())->method("set")->with("dave:foo", "bar", 60);
        $this->bridge->set("foo", "bar", 60);
    }

    /**
     * Tests using a base Memcached instance
     */
    public function testUsingBaseMemcachedInstance()
    {
        $memcached = $this->getMock(Memcached::class, [], [], "Foo", false);
        $bridge = new MemcachedBridge($memcached);
        $this->assertSame($memcached, $bridge->getMemcached());
    }
}