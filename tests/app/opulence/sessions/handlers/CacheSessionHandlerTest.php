<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the cache session handler
 */
namespace Opulence\Sessions\Handlers;

use Opulence\Cache\ICacheBridge;

class CacheSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CacheSessionHandler The handler to use in tests */
    private $handler = null;
    /** @var ICacheBridge|\PHPUnit_Framework_MockObject_MockObject The bridge to use in tests */
    private $bridge = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->bridge = $this->getMock(ICacheBridge::class);
        $this->handler = new CacheSessionHandler($this->bridge, 123);
    }

    /**
     * Tests that delete is called on destroy
     */
    public function testCacheDeleteIsCalledOnDestroy()
    {
        $this->bridge->expects($this->once())->method("delete")->with("foo");
        $this->handler->destroy("foo");
    }

    /**
     * Tests that get is called on read
     */
    public function testCacheGetIsCalledOnRead()
    {
        $this->bridge->expects($this->once())->method("get")->with("foo");
        $this->handler->read("foo");
    }

    /**
     * Tests that set is called on write
     */
    public function testCacheSetIsCalledOnWrite()
    {
        $this->bridge->expects($this->once())->method("set")->with("foo", "bar", 123);
        $this->handler->write("foo", "bar");
    }

    /**
     * Tests that close returns true
     */
    public function testCloseReturnsTrue()
    {
        $this->assertTrue($this->handler->close());
    }

    /**
     * Tests that gc returns true
     */
    public function testGCReturnsTrue()
    {
        $this->assertTrue($this->handler->gc(60));
    }

    /**
     * Tests that open returns true
     */
    public function testOpenReturnsTrue()
    {
        $this->assertTrue($this->handler->open("foo", "bar"));
    }
}