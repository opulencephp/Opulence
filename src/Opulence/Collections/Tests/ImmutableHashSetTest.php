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

use Opulence\Collections\ImmutableHashSet;
use Opulence\Collections\Tests\Mocks\MockObject;

/**
 * Tests an immutable hash set
 */
class ImmutableHashSetTest extends \PHPUnit\Framework\TestCase
{
    public function testAddingArrayValueIsAcceptable(): void
    {
        $array = ['foo'];
        $set = new ImmutableHashSet([$array]);
        $this->assertTrue($set->containsValue($array));
        $this->assertEquals([$array], $set->toArray());
    }

    public function testAddingPrimitiveValuesIsAcceptable(): void
    {
        $int = 1;
        $string = 'foo';
        $set = new ImmutableHashSet([$int, $string]);
        $this->assertTrue($set->containsValue($int));
        $this->assertTrue($set->containsValue($string));
        $this->assertEquals([$int, $string], $set->toArray());
    }

    public function testAddingResourceValuesIsAcceptable(): void
    {
        $resource = fopen('php://temp', 'r+');
        $set = new ImmutableHashSet([$resource]);
        $this->assertTrue($set->containsValue($resource));
        $this->assertEquals([$resource], $set->toArray());
    }

    public function testAddingValue(): void
    {
        $object = new MockObject('foo');
        $set = new ImmutableHashSet([$object]);
        $this->assertEquals([$object], $set->toArray());
    }

    public function testCheckingExistenceOfValueReturnsWhetherOrNotThatValueExists(): void
    {
        $setWithNoValues = new ImmutableHashSet([]);
        $this->assertFalse($setWithNoValues->containsValue('foo'));
        $setWithStringValue = new ImmutableHashSet(['foo']);
        $this->assertTrue($setWithStringValue->containsValue('foo'));
        $object = new MockObject('bar');
        $setWithObjectValue = new ImmutableHashSet([$object]);
        $this->assertTrue($setWithObjectValue->containsValue($object));
    }

    public function testCountReturnsNumberOfUniqueValuesInSet(): void
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
     * Tests iterating over the values returns the values - not the hash keys
     */
    public function testIteratingOverValuesReturnsValuesNotHashKeys(): void
    {
        $expectedValues = [
            new MockObject(),
            new MockObject()
        ];
        $set = new ImmutableHashSet($expectedValues);
        $actualValues = [];

        foreach ($set as $key => $value) {
            // Make sure the hash keys aren't returned by the iterator
            $this->assertTrue(is_int($key));
            $actualValues[] = $value;
        }

        $this->assertEquals($expectedValues, $actualValues);
    }
}
