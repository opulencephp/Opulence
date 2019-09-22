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

use Opulence\Collections\ImmutableArrayList;
use OutOfRangeException;
use RuntimeException;

/**
 * Tests the immutable array list
 */
class ImmutableArrayListTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckingOffsetExists(): void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertTrue(isset($arrayList[0]));
    }

    public function testContainsValue(): void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertTrue($arrayList->containsValue('foo'));
        $this->assertFalse($arrayList->containsValue('bar'));
    }

    /**
     * Tests that checking if a value exists returns true even if the value is null
     */
    public function tesContainsValueReturnsTrueEvenIfValuesIsNull(): void
    {
        $arrayList = new ImmutableArrayList([null]);
        $this->assertTrue($arrayList->containsValue(null));
    }

    public function testCount(): void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertEquals(1, $arrayList->count());
        $arrayList = new ImmutableArrayList(['foo', 'bar']);
        $this->assertEquals(2, $arrayList->count());
    }

    public function testGetting(): void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertEquals('foo', $arrayList->get(0));
    }

    public function testGettingAsArray(): void
    {
        $arrayList = new ImmutableArrayList(['foo']);
        $this->assertEquals('foo', $arrayList[0]);
    }

    public function testGettingIndexGreaterThanListLengthThrowsException(): void
    {
        $this->expectException(OutOfRangeException::class);
        $arrayList = new ImmutableArrayList(['foo']);
        $arrayList->get(1);
    }

    public function testGettingIndexLessThanZeroThrowsException(): void
    {
        $this->expectException(OutOfRangeException::class);
        $arrayList = new ImmutableArrayList(['foo']);
        $arrayList->get(-1);
    }

    public function testIteratingOverValues(): void
    {
        $arrayList = new ImmutableArrayList(['foo', 'bar']);
        $actualValues = [];

        foreach ($arrayList as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['foo', 'bar'], $actualValues);
    }

    public function testSettingValueThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $arrayList = new ImmutableArrayList([]);
        $arrayList[0] = 'foo';
    }

    public function testToArray(): void
    {
        $arrayList = new ImmutableArrayList(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $arrayList->toArray());
    }

    public function testUnsettingValueThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $arrayList = new ImmutableArrayList(['foo']);
        unset($arrayList[0]);
    }
}
