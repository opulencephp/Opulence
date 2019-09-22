<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Sessions\Tests\Handlers;

use Opulence\Cache\ICacheBridge;
use Opulence\Sessions\Handlers\CacheSessionHandler;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the cache session handler
 */
class CacheSessionHandlerTest extends \PHPUnit\Framework\TestCase
{
    private CacheSessionHandler $handler;
    /** @var ICacheBridge|MockObject The bridge to use in tests */
    private ICacheBridge $bridge;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->bridge = $this->createMock(ICacheBridge::class);
        $this->handler = new CacheSessionHandler($this->bridge, 123);
    }

    /**
     * Tests that delete is called on destroy
     */
    public function testCacheDeleteIsCalledOnDestroy(): void
    {
        $this->bridge->expects($this->once())->method('delete')->with('foo');
        $this->handler->destroy('foo');
    }

    /**
     * Tests that get is called on read
     */
    public function testCacheGetIsCalledOnRead(): void
    {
        $this->bridge->expects($this->once())->method('get')->with('foo')->willReturn('bar');
        $this->assertEquals('bar', $this->handler->read('foo'));
    }

    /**
     * Tests that set is called on write
     */
    public function testCacheSetIsCalledOnWrite(): void
    {
        $this->bridge->expects($this->once())->method('set')->with('foo', 'bar', 123);
        $this->handler->write('foo', 'bar');
    }

    /**
     * Tests that close returns true
     */
    public function testCloseReturnsTrue(): void
    {
        $this->assertTrue($this->handler->close());
    }

    /**
     * Tests that gc returns true
     */
    public function testGCReturnsTrue(): void
    {
        $this->assertTrue($this->handler->gc(60));
    }

    /**
     * Tests that open returns true
     */
    public function testOpenReturnsTrue(): void
    {
        $this->assertTrue($this->handler->open('foo', 'bar'));
    }

    /**
     * Tests reading a non-existent session
     */
    public function testReadingNonExistentSession(): void
    {
        $this->assertEmpty($this->handler->read('non-existent'));
    }
}
