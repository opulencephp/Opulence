<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\HashSet;
use Opulence\Collections\Tests\Mocks\MockObject;

/**
 * Tests a hash set
 */
class HashSetTest extends \PHPUnit\Framework\TestCase
{
    /** @var HashSet The set to use in tests */
    private $set = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->set = new HashSet();
    }

    /**
     * Tests that adding an array value is acceptable
     */
    public function testAddingArrayValueIsAcceptable() : void
    {
        $array = ['foo'];
        $this->set->add($array);
        $this->assertTrue($this->set->containsValue($array));
        $this->assertEquals([$array], $this->set->toArray());
    }

    /**
     * Tests that adding primitive values is acceptable
     */
    public function testAddingPrimitiveValuesIsAcceptable() : void
    {
        $int = 1;
        $string = 'foo';
        $this->set->add($int);
        $this->set->add($string);
        $this->assertTrue($this->set->containsValue($int));
        $this->assertTrue($this->set->containsValue($string));
        $this->assertEquals([$int, $string], $this->set->toArray());
    }

    /**
     * Tests that adding resource values is acceptable
     */
    public function testAddingResourceValuesIsAcceptable() : void
    {
        $resource = fopen('php://temp', 'r+');
        $this->set->add($resource);
        $this->assertTrue($this->set->containsValue($resource));
        $this->assertEquals([$resource], $this->set->toArray());
    }

    /**
     * Tests adding a value
     */
    public function testAddingValue() : void
    {
        $object = new MockObject();
        $this->set->add($object);
        $this->assertEquals([$object], $this->set->toArray());
    }

    /**
     * Tests that checking the existence of a value returns whether or not that value exists
     */
    public function testCheckingExistenceOfValueReturnsWhetherOrNotThatValueExists() : void
    {
        $this->assertFalse($this->set->containsValue('foo'));
        $this->set->add('foo');
        $this->assertTrue($this->set->containsValue('foo'));
        $object = new MockObject();
        $this->assertFalse($this->set->containsValue($object));
        $this->set->add($object);
        $this->assertTrue($this->set->containsValue($object));
    }

    /**
     * Tests that clearing a set removes all the values
     */
    public function testClearingSetRemovesAllValues() : void
    {
        $this->set->add(new MockObject());
        $this->set->clear();
        $this->assertEquals([], $this->set->toArray());
    }

    /**
     * Tests that counting returns the number of unique values in a set
     */
    public function testCountReturnsNumberOfUniqueValuesInSet() : void
    {
        $object1 = new MockObject();
        $object2 = new MockObject();
        $this->assertEquals(0, $this->set->count());
        $this->set->add($object1);
        $this->assertEquals(1, $this->set->count());
        $this->set->add($object1);
        $this->assertEquals(1, $this->set->count());
        $this->set->add($object2);
        $this->assertEquals(2, $this->set->count());
    }

    /**
     * Tests that equal but not same objects are not intersected
     */
    public function testEqualButNotSameObjectsAreNotIntersected() : void
    {
        $object1 = new MockObject();
        $object2 = clone $object1;
        $this->set->add($object1);
        $this->set->intersect([$object2]);
        $this->assertEquals([], $this->set->toArray());
    }

    /**
     * Tests that intersecting values intersects that values of the set and the array
     */
    public function testIntersectingIntersectsValuesOfSetAndArray() : void
    {
        $object1 = new MockObject();
        $object2 = new MockObject();
        $this->set->add($object1);
        $this->set->add($object2);
        $this->set->intersect([$object2]);
        $this->assertEquals([$object2], $this->set->toArray());
    }

    /**
     * Tests iterating over the values returns the values - not the hash keys
     */
    public function testIteratingOverValuesReturnsValuesNotHashKeys() : void
    {
        $expectedValues = [
            new MockObject(),
            new MockObject()
        ];
        $this->set->addRange($expectedValues);
        $actualValues = [];

        foreach ($this->set as $key => $value) {
            // Make sure the hash keys aren't returned by the iterator
            $this->assertTrue(is_int($key));
            $actualValues[] = $value;
        }

        $this->assertEquals($expectedValues, $actualValues);
    }

    /**
     * Tests removing a value
     */
    public function testRemovingValue() : void
    {
        $object = new MockObject();
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
        $object = new MockObject();
        $this->set->add($object);
        $this->set->union(['bar', 'baz']);
        $this->assertEquals([$object, 'bar', 'baz'], $this->set->toArray());
    }
}
