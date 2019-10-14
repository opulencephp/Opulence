<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cache\TestsTemp;

use Memcached;
use Opulence\Cache\MemcachedBridge;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the Memcached bridge
 */
class MemcachedBridgeTest extends \PHPUnit\Framework\TestCase
{
    private MemcachedBridge $bridge;
    /** @var Memcached|MockObject The Memcached driver */
    private Memcached $memcached;

    protected function setUp(): void
    {
        $this->memcached = $this->getMockBuilder(Memcached::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->bridge = new MemcachedBridge($this->memcached, 'dave:');
    }

    public function testCheckingIfKeyExists(): void
    {
        $this->memcached->expects($this->at(0))
            ->method('get')
            ->willReturn(false);
        $this->memcached->expects($this->at(1))
            ->method('get')
            ->willReturn('bar');
        $this->assertFalse($this->bridge->has('foo'));
        $this->assertTrue($this->bridge->has('foo'));
    }

    public function testDecrementingReturnsCorrectValues(): void
    {
        $this->memcached->expects($this->at(0))
            ->method('decrement')
            ->with('dave:foo', 1)
            ->willReturn(10);
        $this->memcached->expects($this->at(1))
            ->method('decrement')
            ->with('dave:foo', 5)
            ->willReturn(5);
        // Test using default value
        $this->assertEquals(10, $this->bridge->decrement('foo'));
        // Test using a custom value
        $this->assertEquals(5, $this->bridge->decrement('foo', 5));
    }

    public function testDeletingKey(): void
    {
        $this->memcached->expects($this->once())
            ->method('delete')
            ->with('dave:foo');
        $this->bridge->delete('foo');
    }

    public function testDriverIsCorrectInstance(): void
    {
        $this->assertSame($this->memcached, $this->bridge->getMemcached());
    }

    public function testErrorDuringGetWillReturnNull(): void
    {
        $this->memcached->expects($this->once())
            ->method('get')
            ->willReturn('bar');
        $this->memcached->expects($this->once())
            ->method('getResultCode')
            ->willReturn(1);
        $this->assertNull($this->bridge->get('foo'));
    }

    public function testFlushing(): void
    {
        $this->memcached->expects($this->once())
            ->method('flush');
        $this->bridge->flush();
    }

    public function testGetWorks(): void
    {
        $this->memcached->expects($this->once())
            ->method('get')
            ->willReturn('bar');
        $this->memcached->expects($this->once())
            ->method('getResultCode')
            ->willReturn(0);
        $this->assertEquals('bar', $this->bridge->get('foo'));
    }

    public function testIncrementingReturnsCorrectValues(): void
    {
        $this->memcached->expects($this->at(0))
            ->method('increment')
            ->with('dave:foo', 1)
            ->willReturn(2);
        $this->memcached->expects($this->at(1))
            ->method('increment')
            ->with('dave:foo', 5)
            ->willReturn(7);
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment('foo'));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment('foo', 5));
    }

    public function testNullIsReturnedOnMiss(): void
    {
        $this->memcached->expects($this->once())
            ->method('get')
            ->willReturn(false);
        $this->assertNull($this->bridge->get('foo'));
    }

    public function testSettingValue(): void
    {
        $this->memcached->expects($this->once())
            ->method('set')
            ->with('dave:foo', 'bar', 60);
        $this->bridge->set('foo', 'bar', 60);
    }

    public function testUsingBaseMemcachedInstance(): void
    {
        /** @var Memcached|MockObject $memcached */
        $memcached = $this->getMockBuilder(Memcached::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $bridge = new MemcachedBridge($memcached);
        $this->assertSame($memcached, $bridge->getMemcached());
    }
}
