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

use Opulence\Sessions\Handlers\ArraySessionHandler;

/**
 * Tests the array session handler
 */
class ArraySessionHandlerTest extends \PHPUnit\Framework\TestCase
{
    private ArraySessionHandler $handler;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->handler = new ArraySessionHandler();
    }

    /**
     * Tests the close function
     */
    public function testClose(): void
    {
        $this->assertTrue($this->handler->close());
    }

    /**
     * Tests garbage collection
     */
    public function testGarbageCollection(): void
    {
        $this->assertTrue($this->handler->gc(-1));
    }

    /**
     * Tests the open function
     */
    public function testOpen(): void
    {
        $this->assertTrue($this->handler->open('foo', '123'));
    }

    /**
     * Tests reading a non-existent session
     */
    public function testReadingNonExistentSession(): void
    {
        $this->assertEmpty($this->handler->read('non-existent'));
    }

    /**
     * Tests writing a session
     */
    public function testWritingSession(): void
    {
        $this->handler->write('foo', 'bar');
        $this->assertEquals('bar', $this->handler->read('foo'));
    }
}
