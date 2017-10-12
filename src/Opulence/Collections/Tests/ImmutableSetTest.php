<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\ImmutableSet;
use Opulence\Collections\Tests\Mocks\SerializableObject;
use Opulence\Collections\Tests\Mocks\UnserializableObject;
use RuntimeException;

/**
 * Tests an immutable set
 */
class ImmutableSetTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests adding a value
     */
    public function testAddingValue() : void
    {
        $object = new SerializableObject('foo');
        $set = new ImmutableSet([$object]);
        $this->assertEquals([$object], $set->toArray());
    }

    /**
     * Tests that adding an unserializable object throws an exception
     */
    public function testAddingUnserializableObjectThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        new ImmutableSet([new UnserializableObject()]);
    }

    /**
     * Tests that checking the existence of a value returns whether or not that value exists
     */
    public function testCheckingExistenceOfValueReturnsWhetherOrNotThatValueExists() : void
    {
        $setWithNoValues = new ImmutableSet([]);
        $this->assertFalse($setWithNoValues->containsValue('foo'));
        $setWithStringValue = new ImmutableSet(['foo']);
        $this->assertTrue($setWithStringValue->containsValue('foo'));
        $object = new SerializableObject('bar');
        $setWithObjectValue = new ImmutableSet([$object]);
        $this->assertTrue($setWithObjectValue->containsValue($object));
    }

    /**
     * Tests that checking the existence of an unserializable object throws an exception
     */
    public function testCheckingExistenceOfUnserializableObjectThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        new ImmutableSet([new UnserializableObject()]);
    }

    /**
     * Tests that counting returns the number of unique values in a set
     */
    public function testCountReturnsNumberOfUniqueValuesInSet() : void
    {
        $object1 = new SerializableObject('foo');
        $object2 = new SerializableObject('bar');
        $this->assertEquals(0, (new ImmutableSet([]))->count());
        $setWithOneValue = new ImmutableSet([$object1]);
        $this->assertEquals(1, $setWithOneValue->count());
        $setWithOneUniqueValue = new ImmutableSet([$object1, $object1]);
        $this->assertEquals(1, $setWithOneUniqueValue->count());
        $setWithTwoalues = new ImmutableSet([$object1, $object2]);
        $this->assertEquals(2, $setWithTwoalues->count());
    }

    /**
     * Tests that getting a value throws an exception
     */
    public function testGettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $set = new ImmutableSet([]);
        $set[new SerializableObject('foo')];
    }

    /**
     * Tests that using isset throws an exception
     */
    public function testIssetThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $set = new ImmutableSet([]);
        isset($set[new SerializableObject('foo')]);
    }

    /**
     * Tests unsetting an index throws an exception
     */
    public function testUnsettingIndexThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $set = new ImmutableSet([]);
        unset($set[new SerializableObject('foo')]);
    }
}
