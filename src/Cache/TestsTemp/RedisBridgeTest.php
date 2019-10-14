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

use Opulence\Cache\RedisBridge;
use PHPUnit\Framework\MockObject\MockObject;
use Redis;

/**
 * Tests the Redis bridge
 */
class RedisBridgeTest extends \PHPUnit\Framework\TestCase
{
    private RedisBridge $bridge;
    /** @var Redis|MockObject The Redis driver */
    private Redis $redis;

    protected function setUp(): void
    {
        $this->redis = $this->createMock(Redis::class);
        $this->bridge = new RedisBridge($this->redis, 'dave:');
    }

    public function testCheckingIfKeyExists(): void
    {
        $this->redis->expects($this->at(0))
            ->method('get')
            ->willReturn(false);
        $this->redis->expects($this->at(1))
            ->method('get')
            ->willReturn('bar');
        $this->assertFalse($this->bridge->has('foo'));
        $this->assertTrue($this->bridge->has('foo'));
    }

    public function testDecrementingReturnsCorrectValues(): void
    {
        $this->redis->expects($this->at(0))
            ->method('decrBy')
            ->with('dave:foo', 1)
            ->willReturn(10);
        $this->redis->expects($this->at(1))
            ->method('decrBy')
            ->with('dave:foo', 5)
            ->willReturn(5);
        // Test using default value
        $this->assertEquals(10, $this->bridge->decrement('foo'));
        // Test using a custom value
        $this->assertEquals(5, $this->bridge->decrement('foo', 5));
    }

    public function testDeletingKey(): void
    {
        $this->redis->expects($this->once())
            ->method('del')
            ->with('dave:foo');
        $this->bridge->delete('foo');
    }

    public function testDriverIsCorrectInstance(): void
    {
        $this->assertSame($this->redis, $this->bridge->getRedis());
    }

    public function testFlushing(): void
    {
        $this->redis->expects($this->once())
            ->method('flushAll');
        $this->bridge->flush();
    }

    public function testGetWorks(): void
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->willReturn('bar');
        $this->assertEquals('bar', $this->bridge->get('foo'));
    }

    public function testIncrementingReturnsCorrectValues(): void
    {
        $this->redis->expects($this->at(0))
            ->method('incrBy')
            ->with('dave:foo', 1)
            ->willReturn(2);
        $this->redis->expects($this->at(1))
            ->method('incrBy')
            ->with('dave:foo', 5)
            ->willReturn(7);
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment('foo'));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment('foo', 5));
    }

    public function testNullIsReturnedOnMiss(): void
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->willReturn(false);
        $this->assertNull($this->bridge->get('foo'));
    }

    public function testSettingValue(): void
    {
        $this->redis->expects($this->once())
            ->method('setEx')
            ->with('dave:foo', 60, 'bar');
        $this->bridge->set('foo', 'bar', 60);
    }

    public function testUsingBaseRedisInstance(): void
    {
        /** @var Redis|MockObject $redis */
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bridge = new RedisBridge($redis);
        $this->assertSame($redis, $bridge->getRedis());
    }
}
