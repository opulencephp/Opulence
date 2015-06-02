<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Redis bridge
 */
namespace RDev\Cache;
use RDev\Redis\Server;
use RDev\Redis\TypeMapper;
use RDev\Tests\Redis\Mocks\RDevPHPRedis;

// To get around having to install Redis just to run tests, include a mock Redis class
if(!class_exists("Redis"))
{
    require_once __DIR__ . "/../redis/mocks/redis.php";
}

class RedisBridgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var RedisBridge The bridge to use in tests */
    private $bridge = null;
    /** @var RDevPHPRedis The Redis driver */
    private $redis = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $constructorParams = [$this->getMock(Server::class), $this->getMock(TypeMapper::class)];
        $this->redis = $this->getMock(RDevPHPRedis::class, [], $constructorParams);
        $this->bridge = new RedisBridge($this->redis, "dave:");
    }

    /**
     * Tests checking if a key exists
     */
    public function testCheckingIfKeyExists()
    {
        $this->redis->expects($this->at(0))->method("get")->will($this->returnValue(false));
        $this->redis->expects($this->at(1))->method("get")->will($this->returnValue("bar"));
        $this->assertFalse($this->bridge->has("foo"));
        $this->assertTrue($this->bridge->has("foo"));
    }

    /**
     * Tests decrementing returns correct values
     */
    public function testDecrementingReturnsCorrectValues()
    {
        $this->redis->expects($this->at(0))->method("decrBy")->with("dave:foo", 1)->will($this->returnValue(10));
        $this->redis->expects($this->at(1))->method("decrBy")->with("dave:foo", 5)->will($this->returnValue(5));
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
        $this->redis->expects($this->once())->method("del")->with("dave:foo");
        $this->bridge->delete("foo");
    }

    /**
     * Tests that the driver is the correct instance of Redis
     */
    public function testDriverIsCorrectInstance()
    {
        $this->assertSame($this->redis, $this->bridge->getDriver());
    }

    /**
     * Tests flushing the database
     */
    public function testFlushing()
    {
        $this->redis->expects($this->once())->method("flushAll");
        $this->bridge->flush();
    }

    /**
     * Tests that getting a value works
     */
    public function testGetWorks()
    {
        $this->redis->expects($this->once())->method("get")->will($this->returnValue("bar"));
        $this->assertEquals("bar", $this->bridge->get("foo"));
    }

    /**
     * Tests incrementing returns correct values
     */
    public function testIncrementingReturnsCorrectValues()
    {
        $this->redis->expects($this->at(0))->method("incrBy")->with("dave:foo", 1)->will($this->returnValue(2));
        $this->redis->expects($this->at(1))->method("incrBy")->with("dave:foo", 5)->will($this->returnValue(7));
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
        $this->redis->expects($this->once())->method("get")->will($this->returnValue(false));
        $this->assertNull($this->bridge->get("foo"));
    }

    /**
     * Tests setting a value
     */
    public function testSettingValue()
    {
        $this->redis->expects($this->once())->method("set")->with("dave:foo", "bar");
        $this->bridge->set("foo", "bar");
    }
}