<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cache\Tests;

use Opulence\Cache\RedisBridge;
use Opulence\Redis\Redis;
use Predis\Client;

/**
 * Tests the Redis bridge
 */
class RedisBridgeTest extends \PHPUnit\Framework\TestCase
{
    /** @var RedisBridge The bridge to use in tests */
    private $bridge = null;
    /** @var Redis|\PHPUnit_Framework_MockObject_MockObject The Redis driver */
    private $redis = null;
    /** @var Client|\PHPUnit_Framework_MockObject_MockObject The Redis client */
    private $client = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $methods = ['get', 'decrBy', 'del', 'flushAll', 'incrBy', 'setEx'];
        $this->client = $this->getMockBuilder(Client::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        $this->redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->redis->expects($this->any())
            ->method('getClient')
            ->with('default')
            ->willReturn($this->client);
        $this->bridge = new RedisBridge($this->redis, 'default', 'dave:');
    }

    /**
     * Tests checking if a key exists
     */
    public function testCheckingIfKeyExists()
    {
        $this->client->expects($this->at(0))
            ->method('get')
            ->willReturn(false);
        $this->client->expects($this->at(1))
            ->method('get')
            ->willReturn('bar');
        $this->assertFalse($this->bridge->has('foo'));
        $this->assertTrue($this->bridge->has('foo'));
    }

    /**
     * Tests decrementing returns correct values
     */
    public function testDecrementingReturnsCorrectValues()
    {
        $this->client->expects($this->at(0))
            ->method('decrBy')
            ->with('dave:foo', 1)
            ->willReturn(10);
        $this->client->expects($this->at(1))
            ->method('decrBy')
            ->with('dave:foo', 5)
            ->willReturn(5);
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
        $this->client->expects($this->once())
            ->method('del')
            ->with('dave:foo');
        $this->bridge->delete('foo');
    }

    /**
     * Tests that the driver is the correct instance of Redis
     */
    public function testDriverIsCorrectInstance()
    {
        $this->assertSame($this->redis, $this->bridge->getRedis());
    }

    /**
     * Tests flushing the database
     */
    public function testFlushing()
    {
        $this->client->expects($this->once())
            ->method('flushAll');
        $this->bridge->flush();
    }

    /**
     * Tests that getting a value works
     */
    public function testGetWorks()
    {
        $this->client->expects($this->once())
            ->method('get')
            ->willReturn('bar');
        $this->assertEquals('bar', $this->bridge->get('foo'));
    }

    /**
     * Tests incrementing returns correct values
     */
    public function testIncrementingReturnsCorrectValues()
    {
        $this->client->expects($this->at(0))
            ->method('incrBy')
            ->with('dave:foo', 1)
            ->willReturn(2);
        $this->client->expects($this->at(1))
            ->method('incrBy')
            ->with('dave:foo', 5)
            ->willReturn(7);
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment('foo'));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment('foo', 5));
    }

    /**
     * Tests that null is returned on cache miss
     */
    public function testNullIsReturnedOnMiss()
    {
        $this->client->expects($this->once())
            ->method('get')
            ->willReturn(false);
        $this->assertNull($this->bridge->get('foo'));
    }

    /**
     * Tests setting a value
     */
    public function testSettingValue()
    {
        $this->client->expects($this->once())
            ->method('setEx')
            ->with('dave:foo', 60, 'bar');
        $this->bridge->set('foo', 'bar', 60);
    }

    /**
     * Tests using a base Redis instance
     */
    public function testUsingBaseRedisInstance()
    {
        /** @var Redis|\PHPUnit_Framework_MockObject_MockObject $redis */
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bridge = new RedisBridge($redis);
        $this->assertSame($redis, $bridge->getRedis());
    }

    /**
     * Tests using a client beside the default one
     */
    public function testUsingClientBesidesDefaultOne()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects($this->any())
            ->method('get')
            ->with('bar')
            ->willReturn('baz');
        /** @var Redis|\PHPUnit_Framework_MockObject_MockObject $redis */
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redis->expects($this->any())
            ->method('getClient')
            ->with('foo')
            ->willReturn($client);
        $bridge = new RedisBridge($redis, 'foo');
        $this->assertEquals('baz', $bridge->get('bar'));
    }
}
