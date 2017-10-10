<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cache\Tests;

use Memcached as Client;
use Opulence\Cache\MemcachedBridge;
use Opulence\Memcached\Memcached;

/**
 * Tests the Memcached bridge
 */
class MemcachedBridgeTest extends \PHPUnit\Framework\TestCase
{
    /** @var MemcachedBridge The bridge to use in tests */
    private $bridge = null;
    /** @var Memcached|\PHPUnit_Framework_MockObject_MockObject The Memcached driver */
    private $memcached = null;
    /** @var Client|\PHPUnit_Framework_MockObject_MockObject The client to use in tests */
    private $client = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $methods = ['decrement', 'delete', 'flush', 'get', 'getResultCode', 'increment', 'set'];
        $this->client = $this->getMockBuilder(Client::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        $this->memcached = $this->getMockBuilder(Memcached::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->memcached->expects($this->any())
            ->method('getClient')
            ->with('default')
            ->willReturn($this->client);
        $this->bridge = new MemcachedBridge($this->memcached, 'default', 'dave:');
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
            ->method('decrement')
            ->with('dave:foo', 1)
            ->willReturn(10);
        $this->client->expects($this->at(1))
            ->method('decrement')
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
            ->method('delete')
            ->with('dave:foo');
        $this->bridge->delete('foo');
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
        $this->client->expects($this->once())
            ->method('get')
            ->willReturn('bar');
        $this->client->expects($this->once())
            ->method('getResultCode')
            ->willReturn(1);
        $this->assertNull($this->bridge->get('foo'));
    }

    /**
     * Tests flushing the database
     */
    public function testFlushing()
    {
        $this->client->expects($this->once())
            ->method('flush');
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
        $this->client->expects($this->once())
            ->method('getResultCode')
            ->willReturn(0);
        $this->assertEquals('bar', $this->bridge->get('foo'));
    }

    /**
     * Tests incrementing returns correct values
     */
    public function testIncrementingReturnsCorrectValues()
    {
        $this->client->expects($this->at(0))
            ->method('increment')
            ->with('dave:foo', 1)
            ->willReturn(2);
        $this->client->expects($this->at(1))
            ->method('increment')
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
            ->method('set')
            ->with('dave:foo', 'bar', 60);
        $this->bridge->set('foo', 'bar', 60);
    }

    /**
     * Tests using a base Memcached instance
     */
    public function testUsingBaseMemcachedInstance()
    {
        /** @var Memcached|\PHPUnit_Framework_MockObject_MockObject $memcached */
        $memcached = $this->getMockBuilder(Memcached::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $bridge = new MemcachedBridge($memcached);
        $this->assertSame($memcached, $bridge->getMemcached());
    }

    /**
     * Tests using a client beside the default one
     */
    public function testUsingClientBesidesDefaultOne()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['get', 'getResultCode'])
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects($this->any())
            ->method('get')
            ->with('bar')
            ->willReturn('baz');
        $client->expects($this->any())
            ->method('getResultCode')
            ->willReturn(0);
        /** @var Memcached|\PHPUnit_Framework_MockObject_MockObject $memcached */
        $memcached = $this->getMockBuilder(Memcached::class)
            ->disableOriginalConstructor()
            ->getMock();
        $memcached->expects($this->any())
            ->method('getClient')
            ->with('foo')
            ->willReturn($client);
        $bridge = new MemcachedBridge($memcached, 'foo');
        $this->assertEquals('baz', $bridge->get('bar'));
    }
}
