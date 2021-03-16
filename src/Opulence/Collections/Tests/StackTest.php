<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\Stack;

/**
 * Tests the stack
 */
class StackTest extends \PHPUnit\Framework\TestCase
{
    /** @var Stack The stack to use in tests */
    private $stack = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->stack = new Stack();
    }

    /**
     * Tests clearing
     */
    public function testClearing() : void
    {
        $this->stack->push('foo');
        $this->stack->clear();
        $this->assertEquals([], $this->stack->toArray());
    }

    /**
     * Tests that has returns whether or not the value exists
     */
    public function testContainsValueReturnsWhetherOrNotValueExists() : void
    {
        $this->assertFalse($this->stack->containsValue('foo'));
        $this->stack->push('foo');
        $this->assertTrue($this->stack->containsValue('foo'));
    }

    /**
     * Tests counting
     */
    public function testCounting() : void
    {
        $this->assertEquals(0, $this->stack->count());
        $this->stack->push('foo');
        $this->assertEquals(1, $this->stack->count());
        $this->stack->push('bar');
        $this->assertEquals(2, $this->stack->count());
    }

    /**
     * Tests iterating over the values
     */
    public function testIteratingOverValues() : void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $actualValues = [];

        foreach ($this->stack as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['bar', 'foo'], $actualValues);
    }

    /**
     * Tests that peeking when no values are in the stack returns null
     */
    public function testPeekingWhenNoValuesInStackReturnsNull() : void
    {
        $this->assertNull($this->stack->peek());
    }

    /**
     * Tests that peek returns the top value
     */
    public function testPeekReturnsTopValue() : void
    {
        $this->stack->push('foo');
        $this->assertEquals('foo', $this->stack->peek());
        $this->stack->push('bar');
        $this->assertEquals('bar', $this->stack->peek());
    }

    /**
     * Tests that popping removes the value from the stop of the stack
     */
    public function testPoppingRemovesValueFromTopOfStack() : void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->assertEquals('bar', $this->stack->pop());
        $this->assertEquals(['foo'], $this->stack->toArray());
        $this->assertEquals('foo', $this->stack->pop());
        $this->assertEquals([], $this->stack->toArray());
    }

    /**
     * Tests that popping when no values are in the stack returns null
     */
    public function testPoppingWhenNoValuesAreInStackReturnsNull() : void
    {
        $this->assertNull($this->stack->pop());
    }

    /**
     * Tests that pushing adds the value to the top of the stack
     */
    public function testPushingAddsValueToTopOfStack() : void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->assertEquals('bar', $this->stack->pop());
        $this->assertEquals('foo', $this->stack->pop());
    }

    /**
     * Tests that converting to an array actually converts it
     */
    public function testToArrayConvertsTheStackToArray() : void
    {
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->assertEquals(['bar', 'foo'], $this->stack->toArray());
    }
}
