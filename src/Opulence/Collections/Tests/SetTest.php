<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\Set;
use Opulence\Collections\Tests\Mocks\SerializableObject;
use Opulence\Collections\Tests\Mocks\UnserializableObject;
use RuntimeException;

/**
 * Tests a set
 */
class SetTest extends \PHPUnit\Framework\TestCase
{
    /** @var Set The set to use in tests */
    private $set = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->set = new Set();
    }

    /**
     * Tests adding a value
     */
    public function testAddingValue() : void
    {
        $object = new SerializableObject('foo');
        $this->set->add($object);
        $this->assertEquals([$object], $this->set->toArray());
    }

    /**
     * Tests that adding an unserializable object throws an exception
     */
    public function testAddingUnserializableObjectThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $this->set->add(new UnserializableObject());
    }

    /**
     * Tests that checking the existence of a value returns whether or not that value exists
     */
    public function testCheckingExistenceOfValueReturnsWhetherOrNotThatValueExists() : void
    {
        $this->assertFalse($this->set->containsValue('foo'));
        $this->set->add('foo');
        $this->assertTrue($this->set->containsValue('foo'));
        $object = new SerializableObject('bar');
        $this->assertFalse($this->set->containsValue($object));
        $this->set->add($object);
        $this->assertTrue($this->set->containsValue($object));
    }

    /**
     * Tests that checking the existence of an unserializable object throws an exception
     */
    public function testCheckingExistenceOfUnserializableObjectThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $this->set->containsValue(new UnserializableObject());
    }

    /**
     * Tests that clearing a set removes all the values
     */
    public function testClearingSetRemovesAllValues() : void
    {
        $this->set->add(new SerializableObject('foo'));
        $this->set->clear();
        $this->assertEquals([], $this->set->toArray());
    }

    /**
     * Tests that counting returns the number of unique values in a set
     */
    public function testCountReturnsNumberOfUniqueValuesInSet() : void
    {
        $object1 = new SerializableObject('foo');
        $object2 = new SerializableObject('bar');
        $this->assertEquals(0, $this->set->count());
        $this->set->add($object1);
        $this->assertEquals(1, $this->set->count());
        $this->set->add($object1);
        $this->assertEquals(1, $this->set->count());
        $this->set->add($object2);
        $this->assertEquals(2, $this->set->count());
    }

    /**
     * Tests that getting a value throws an exception
     */
    public function testGettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $this->set[new SerializableObject('foo')];
    }

    /**
     * Tests that intersecting values intersects that values of the set and the array
     */
    public function testIntersectingIntersectsValuesOfSetAndArray() : void
    {
        $object1 = new SerializableObject('foo');
        $object2 = new SerializableObject('bar');
        $this->set->add($object1);
        $this->set->add($object2);
        $this->set->intersect(['bar', 'baz']);
        $this->assertEquals(['bar'], $this->set->toArray());
    }

    /**
     * Tests that using isset throws an exception
     */
    public function testIssetThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        isset($this->set[new SerializableObject('foo')]);
    }

    /**
     * Tests that removing an unserializable object throws an exception
     */
    public function testRemovingUnserializableObjectThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $this->set->removeValue(new UnserializableObject());
    }

    /**
     * Tests removing a value
     */
    public function testRemovingValue() : void
    {
        $object = new SerializableObject('foo');
        $this->set->add($object);
        $this->set->removeValue($object);
        $this->assertEquals([], $this->set->toArray());
    }

    /**
     * Tests sorting the list
     */
    public function testSorting() : void
    {
        $comparer = function ($a, $b) {
            if ($a === 'foo') {
                return 1;
            }

            return -1;
        };
        $this->set->add('foo');
        $this->set->add('bar');
        $this->set->sort($comparer);
        $this->assertEquals(['bar', 'foo'], $this->set->toArray());
    }

    /**
     * Tests that unioning values unions that values of the set and the array
     */
    public function testUnionUnionsValuesOfSetAndArray() : void
    {
        $object = new SerializableObject('foo');
        $this->set->add($object);
        $this->set->union(['bar', 'baz']);
        $this->assertEquals(['foo', 'bar', 'baz'], $this->set->toArray());
    }

    /**
     * Tests unsetting an index throws an exception
     */
    public function testUnsettingIndexThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        unset($this->set[new SerializableObject('foo')]);
    }
}
