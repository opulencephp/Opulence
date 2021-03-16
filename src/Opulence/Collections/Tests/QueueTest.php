<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\Queue;

/**
 * Tests the queue
 */
class QueueTest extends \PHPUnit\Framework\TestCase
{
    /** @var Queue The queue to use in tests */
    private $queue = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->queue = new Queue();
    }

    /**
     * Tests clearing
     */
    public function testClearing() : void
    {
        $this->queue->enqueue('foo');
        $this->queue->clear();
        $this->assertEquals([], $this->queue->toArray());
    }

    /**
     * Tests that has returns whether or not the value exists
     */
    public function testContainsValueReturnsWhetherOrNotValueExists() : void
    {
        $this->assertFalse($this->queue->containsValue('foo'));
        $this->queue->enqueue('foo');
        $this->assertTrue($this->queue->containsValue('foo'));
    }

    /**
     * Tests counting
     */
    public function testCounting() : void
    {
        $this->assertEquals(0, $this->queue->count());
        $this->queue->enqueue('foo');
        $this->assertEquals(1, $this->queue->count());
        $this->queue->enqueue('bar');
        $this->assertEquals(2, $this->queue->count());
    }

    /**
     * Tests that dequeueing removes the value from the beginning of the queue
     */
    public function testDequeuingRemovesValueFromBeginningOfQueue() : void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $this->assertEquals('foo', $this->queue->dequeue());
        $this->assertEquals(['bar'], $this->queue->toArray());
        $this->assertEquals('bar', $this->queue->dequeue());
        $this->assertEquals([], $this->queue->toArray());
    }

    /**
     * Tests that dequeueing when no values are in the queue returns null
     */
    public function testDequeueingWhenNoValuesAreInQueueReturnsNull() : void
    {
        $this->assertNull($this->queue->dequeue());
    }

    /**
     * Tests that enqueue adds the value to the end of the queue
     */
    public function testEnqueueAddsValueToEndOfQueue() : void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $this->assertEquals('foo', $this->queue->dequeue());
        $this->assertEquals('bar', $this->queue->dequeue());
    }

    /**
     * Tests iterating over the values
     */
    public function testIteratingOverValues() : void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $actualValues = [];

        foreach ($this->queue as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['foo', 'bar'], $actualValues);
    }

    /**
     * Tests that peeking when no values are in the queue returns null
     */
    public function testPeekingWhenNoValuesInQueueReturnsNull() : void
    {
        $this->assertNull($this->queue->peek());
    }

    /**
     * Tests that peek returns the value at the beginning
     */
    public function testPeekReturnsValueAtBeginning() : void
    {
        $this->queue->enqueue('foo');
        $this->assertEquals('foo', $this->queue->peek());
        $this->queue->enqueue('bar');
        $this->assertEquals('foo', $this->queue->peek());
    }

    /**
     * Tests that converting to an array actually converts it
     */
    public function testToArrayConvertsTheQueueToArray() : void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $this->assertEquals(['foo', 'bar'], $this->queue->toArray());
    }
}
