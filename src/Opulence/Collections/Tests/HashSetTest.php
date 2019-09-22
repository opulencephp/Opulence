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

use Opulence\Collections\HashSet;
use Opulence\Collections\Tests\Mocks\MockObject;

/**
 * Tests a hash set
 */
class HashSetTest extends \PHPUnit\Framework\TestCase
{
    /** @var HashSet The set to use in tests */
    private $set;

    protected function setUp(): void
    {
        $this->set = new HashSet();
    }

    public function testAddingArrayValueIsAcceptable(): void
    {
        $array = ['foo'];
        $this->set->add($array);
        $this->assertTrue($this->set->containsValue($array));
        $this->assertEquals([$array], $this->set->toArray());
    }

    public function testAddingPrimitiveValuesIsAcceptable(): void
    {
        $int = 1;
        $string = 'foo';
        $this->set->add($int);
        $this->set->add($string);
        $this->assertTrue($this->set->containsValue($int));
        $this->assertTrue($this->set->containsValue($string));
        $this->assertEquals([$int, $string], $this->set->toArray());
    }

    public function testAddingResourceValuesIsAcceptable(): void
    {
        $resource = fopen('php://temp', 'r+b');
        $this->set->add($resource);
        $this->assertTrue($this->set->containsValue($resource));
        $this->assertEquals([$resource], $this->set->toArray());
    }

    public function testAddingValue(): void
    {
        $object = new MockObject();
        $this->set->add($object);
        $this->assertEquals([$object], $this->set->toArray());
    }

    public function testCheckingExistenceOfValueReturnsWhetherOrNotThatValueExists(): void
    {
        $this->assertFalse($this->set->containsValue('foo'));
        $this->set->add('foo');
        $this->assertTrue($this->set->containsValue('foo'));
        $object = new MockObject();
        $this->assertFalse($this->set->containsValue($object));
        $this->set->add($object);
        $this->assertTrue($this->set->containsValue($object));
    }

    public function testClearingSetRemovesAllValues(): void
    {
        $this->set->add(new MockObject());
        $this->set->clear();
        $this->assertEquals([], $this->set->toArray());
    }

    public function testCountReturnsNumberOfUniqueValuesInSet(): void
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

    public function testEqualButNotSameObjectsAreNotIntersected(): void
    {
        $object1 = new MockObject();
        $object2 = clone $object1;
        $this->set->add($object1);
        $this->set->intersect([$object2]);
        $this->assertEquals([], $this->set->toArray());
    }

    public function testIntersectingIntersectsValuesOfSetAndArray(): void
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
    public function testIteratingOverValuesReturnsValuesNotHashKeys(): void
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

    public function testRemovingValue(): void
    {
        $object = new MockObject();
        $this->set->add($object);
        $this->set->removeValue($object);
        $this->assertEquals([], $this->set->toArray());
    }

    public function testSorting(): void
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

    public function testUnionUnionsValuesOfSetAndArray(): void
    {
        $object = new MockObject();
        $this->set->add($object);
        $this->set->union(['bar', 'baz']);
        $this->assertEquals([$object, 'bar', 'baz'], $this->set->toArray());
    }
}
