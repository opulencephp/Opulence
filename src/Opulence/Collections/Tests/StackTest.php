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

use Opulence\Collections\Stack;

/**
 * Tests the stack
 */
class StackTest extends \PHPUnit\Framework\TestCase
{
    /** @var Stack The stack to use in tests */
    private $stack;

    protected function setUp(): void
    {
        $this->stack = new Stack();
    }

    public function testClearing(): void
    {
        $this->stack->push('foo');
        $this->stack->clear();
        $this->assertEquals([], $this->stack->toArray());
    }

    public function testContainsValueReturnsWhetherOrNotValueExists(): void
    {
        $this->assertFalse($this->stack->containsValue('foo'));
        $this->stack->push('foo');
        $this->assertTrue($this->stack->containsValue('foo'));
    }

    public function testCounting(): void
    {
        $this->assertEquals(0, $this->stack->count());
        $this->stack->push('foo');
        $this->assertEquals(1, $this->stack->count());
        $this->stack->push('bar');
        $this->assertEquals(2, $this->stack->count());
    }

    public function testIteratingOverValues(): void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $actualValues = [];

        foreach ($this->stack as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['bar', 'foo'], $actualValues);
    }

    public function testPeekingWhenNoValuesInStackReturnsNull(): void
    {
        $this->assertNull($this->stack->peek());
    }

    public function testPeekReturnsTopValue(): void
    {
        $this->stack->push('foo');
        $this->assertEquals('foo', $this->stack->peek());
        $this->stack->push('bar');
        $this->assertEquals('bar', $this->stack->peek());
    }

    public function testPoppingRemovesValueFromTopOfStack(): void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->assertEquals('bar', $this->stack->pop());
        $this->assertEquals(['foo'], $this->stack->toArray());
        $this->assertEquals('foo', $this->stack->pop());
        $this->assertEquals([], $this->stack->toArray());
    }

    public function testPoppingWhenNoValuesAreInStackReturnsNull(): void
    {
        $this->assertNull($this->stack->pop());
    }

    public function testPushingAddsValueToTopOfStack(): void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->assertEquals('bar', $this->stack->pop());
        $this->assertEquals('foo', $this->stack->pop());
    }

    public function testToArrayConvertsTheStackToArray(): void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->assertEquals(['bar', 'foo'], $this->stack->toArray());
    }
}
