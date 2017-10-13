<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\ImmutableHashSet;
use Opulence\Collections\Tests\Mocks\MockObject;
use RuntimeException;

/**
 * Tests an immutable hash set
 */
class ImmutableHashSetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that adding an array value is acceptable
     */
    public function testAddingArrayValueIsAcceptable() : void
    {
        $array = ['foo'];
        $set = new ImmutableHashSet([$array]);
        $this->assertTrue($set->containsValue($array));
        $this->assertEquals([$array], $set->toArray());
    }

    /**
     * Tests that adding primitive values is acceptable
     */
    public function testAddingPrimitiveValuesIsAcceptable() : void
    {
        $int = 1;
        $string = 'foo';
        $set = new ImmutableHashSet([$int, $string]);
        $this->assertTrue($set->containsValue($int));
        $this->assertTrue($set->containsValue($string));
        $this->assertEquals([$int, $string], $set->toArray());
    }

    /**
     * Tests that adding resource values is acceptable
     */
    public function testAddingResourceValuesIsAcceptable() : void
    {
        $resource = fopen('php://temp', 'r+');
        $set = new ImmutableHashSet([$resource]);
        $this->assertTrue($set->containsValue($resource));
        $this->assertEquals([$resource], $set->toArray());
    }

    /**
     * Tests adding a value
     */
    public function testAddingValue() : void
    {
        $object = new MockObject('foo');
        $set = new ImmutableHashSet([$object]);
        $this->assertEquals([$object], $set->toArray());
    }

    /**
     * Tests that checking the existence of a value returns whether or not that value exists
     */
    public function testCheckingExistenceOfValueReturnsWhetherOrNotThatValueExists() : void
    {
        $setWithNoValues = new ImmutableHashSet([]);
        $this->assertFalse($setWithNoValues->containsValue('foo'));
        $setWithStringValue = new ImmutableHashSet(['foo']);
        $this->assertTrue($setWithStringValue->containsValue('foo'));
        $object = new MockObject('bar');
        $setWithObjectValue = new ImmutableHashSet([$object]);
        $this->assertTrue($setWithObjectValue->containsValue($object));
    }

    /**
     * Tests that counting returns the number of unique values in a set
     */
    public function testCountReturnsNumberOfUniqueValuesInSet() : void
    {
        $object1 = new MockObject('foo');
        $object2 = new MockObject('bar');
        $this->assertEquals(0, (new ImmutableHashSet([]))->count());
        $setWithOneValue = new ImmutableHashSet([$object1]);
        $this->assertEquals(1, $setWithOneValue->count());
        $setWithOneUniqueValue = new ImmutableHashSet([$object1, $object1]);
        $this->assertEquals(1, $setWithOneUniqueValue->count());
        $setWithTwoalues = new ImmutableHashSet([$object1, $object2]);
        $this->assertEquals(2, $setWithTwoalues->count());
    }

    /**
     * Tests that getting a value throws an exception
     */
    public function testGettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $set = new ImmutableHashSet([]);
        $set[new MockObject('foo')];
    }

    /**
     * Tests that using isset throws an exception
     */
    public function testIssetThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $set = new ImmutableHashSet([]);
        isset($set[new MockObject('foo')]);
    }

    /**
     * Tests unsetting an index throws an exception
     */
    public function testUnsettingIndexThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $set = new ImmutableHashSet([]);
        unset($set[new MockObject('foo')]);
    }
}
