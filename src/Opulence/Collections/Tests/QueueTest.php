<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Collections\Tests;

use Opulence\Collections\Queue;

/**
 * Tests the queue
 */
class QueueTest extends \PHPUnit\Framework\TestCase
{
    /** @var Queue The queue to use in tests */
    private $queue;

    protected function setUp(): void
    {
        $this->queue = new Queue();
    }

    public function testClearing(): void
    {
        $this->queue->enqueue('foo');
        $this->queue->clear();
        $this->assertEquals([], $this->queue->toArray());
    }

    public function testContainsValueReturnsWhetherOrNotValueExists(): void
    {
        $this->assertFalse($this->queue->containsValue('foo'));
        $this->queue->enqueue('foo');
        $this->assertTrue($this->queue->containsValue('foo'));
    }

    public function testCounting(): void
    {
        $this->assertEquals(0, $this->queue->count());
        $this->queue->enqueue('foo');
        $this->assertEquals(1, $this->queue->count());
        $this->queue->enqueue('bar');
        $this->assertEquals(2, $this->queue->count());
    }

    public function testDequeuingRemovesValueFromBeginningOfQueue(): void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $this->assertEquals('foo', $this->queue->dequeue());
        $this->assertEquals(['bar'], $this->queue->toArray());
        $this->assertEquals('bar', $this->queue->dequeue());
        $this->assertEquals([], $this->queue->toArray());
    }

    public function testDequeueingWhenNoValuesAreInQueueReturnsNull(): void
    {
        $this->assertNull($this->queue->dequeue());
    }

    public function testEnqueueAddsValueToEndOfQueue(): void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $this->assertEquals('foo', $this->queue->dequeue());
        $this->assertEquals('bar', $this->queue->dequeue());
    }

    public function testIteratingOverValues(): void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $actualValues = [];

        foreach ($this->queue as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['foo', 'bar'], $actualValues);
    }

    public function testPeekingWhenNoValuesInQueueReturnsNull(): void
    {
        $this->assertNull($this->queue->peek());
    }

    public function testPeekReturnsValueAtBeginning(): void
    {
        $this->queue->enqueue('foo');
        $this->assertEquals('foo', $this->queue->peek());
        $this->queue->enqueue('bar');
        $this->assertEquals('foo', $this->queue->peek());
    }

    public function testToArrayConvertsTheQueueToArray(): void
    {
        $this->queue->enqueue('foo');
        $this->queue->enqueue('bar');
        $this->assertEquals(['foo', 'bar'], $this->queue->toArray());
    }
}
