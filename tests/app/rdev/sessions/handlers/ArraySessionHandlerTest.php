<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the array session handler
 */
namespace RDev\Sessions\Handlers;

class ArraySessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArraySessionHandler The handler to use in tests */
    private $handler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->handler = new ArraySessionHandler();
    }

    /**
     * Tests the close function
     */
    public function testClose()
    {
        $this->assertTrue($this->handler->close());
    }

    /**
     * Tests garbage collection
     */
    public function testGarbageCollection()
    {
        $this->assertTrue($this->handler->gc(-1));
    }

    /**
     * Tests the open function
     */
    public function testOpen()
    {
        $this->assertTrue($this->handler->open("foo", "123"));
    }

    /**
     * Tests reading a non-existent session
     */
    public function testReadingNonExistentSession()
    {
        $this->assertEmpty($this->handler->read("non-existent"));
    }

    /**
     * Tests writing a session
     */
    public function testWritingSession()
    {
        $this->handler->write("foo", "bar");
        $this->assertEquals("bar", $this->handler->read("foo"));
    }
}