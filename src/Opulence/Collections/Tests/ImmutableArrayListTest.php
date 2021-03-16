<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\ImmutableArrayList;
use OutOfRangeException;
use RuntimeException;

/**
 * Tests the immutable array list
 */
class ImmutableArrayListTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests checking if an offset exists
     */
    public function testCheckingOffsetExists() : void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertTrue(isset($arrayList[0]));
    }

    /**
     * Tests whether the list has a certain parameter
     */
    public function testContainsValue() : void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertTrue($arrayList->containsValue('foo'));
        $this->assertFalse($arrayList->containsValue('bar'));
    }

    /**
     * Tests that checking if a value exists returns true even if the value is null
     */
    public function tesContainsValueReturnsTrueEvenIfValuesIsNull() : void
    {
        $arrayList = new ImmutableArrayList([null]);
        $this->assertTrue($arrayList->containsValue(null));
    }

    /**
     * Tests counting
     */
    public function testCount() : void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertEquals(1, $arrayList->count());
        $arrayList = new ImmutableArrayList(['foo', 'bar']);
        $this->assertEquals(2, $arrayList->count());
    }

    /**
     * Tests getting a parameter
     */
    public function testGetting() : void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertEquals('foo', $arrayList->get(0));
    }

    /**
     * Tests getting as array
     */
    public function testGettingAsArray() : void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertEquals('foo', $arrayList[0]);
    }

    /**
     * Tests that getting an index greater than the list length throws an exception
     */
    public function testGettingIndexGreaterThanListLengthThrowsException() : void
    {
        $this->expectException(OutOfRangeException::class);
        $arrayList = new ImmutableArrayList(['foo']);
        $arrayList->get(1);
    }

    /**
     * Tests that getting an index less than zero throws an exception
     */
    public function testGettingIndexLessThanZeroThrowsException() : void
    {
        $this->expectException(OutOfRangeException::class);
        $arrayList = new ImmutableArrayList(['foo']);
        $arrayList->get(-1);
    }

    /**
     * Tests iterating over the values
     */
    public function testIteratingOverValues() : void
    {
        $arrayList = new ImmutableArrayList(['foo', 'bar']);
        $actualValues = [];

        foreach ($arrayList as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['foo', 'bar'], $actualValues);
    }

    /**
     * Tests that setting a value throws an exception
     */
    public function testSettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $arrayList = new ImmutableArrayList([]);
        $arrayList[0] = 'foo';
    }

    /**
     * Tests getting all the parameters
     */
    public function testToArray() : void
    {
        $arrayList = new ImmutableArrayList(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $arrayList->toArray());
    }

    /**
     * Tests that unsetting a value throws an exception
     */
    public function testUnsettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $arrayList = new ImmutableArrayList(['foo']);
        unset($arrayList[0]);
    }
}
