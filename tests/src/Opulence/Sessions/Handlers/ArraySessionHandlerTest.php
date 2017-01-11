<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Sessions\Handlers;

/**
 * Tests the array session handler
 */
class ArraySessionHandlerTest extends \PHPUnit\Framework\TestCase
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
        $this->assertTrue($this->handler->open('foo', '123'));
    }

    /**
     * Tests reading a non-existent session
     */
    public function testReadingNonExistentSession()
    {
        $this->assertEmpty($this->handler->read('non-existent'));
    }

    /**
     * Tests writing a session
     */
    public function testWritingSession()
    {
        $this->handler->write('foo', 'bar');
        $this->assertEquals('bar', $this->handler->read('foo'));
    }
}
