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

use Opulence\Collections\ArrayList;
use OutOfRangeException;

/**
 * Tests the array list
 */
class ArrayListTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayList The array list to use in tests */
    private $arrayList;

    protected function setUp(): void
    {
        $this->arrayList = new ArrayList();
    }

    public function testAdding(): void
    {
        $this->arrayList->add('foo');
        $this->assertEquals('foo', $this->arrayList->get(0));
    }

    public function testAddingRangeOfValues(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->addRange(['bar', 'baz']);
        $this->assertEquals(['foo', 'bar', 'baz'], $this->arrayList->toArray());
    }

    public function testCheckingOffsetExists(): void
    {
        $this->arrayList->add('foo');
        $this->assertTrue(isset($this->arrayList[0]));
    }

    public function testClearing(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->clear();
        $this->assertEquals([], $this->arrayList->toArray());
    }

    public function testContainsValue(): void
    {
        $this->assertFalse($this->arrayList->containsValue('foo'));
        $this->arrayList->add('foo');
        $this->assertTrue($this->arrayList->containsValue('foo'));
    }

    /**
     * Tests that checking if a value exists returns true even if the value is null
     */
    public function tesContainsValueReturnsTrueEvenIfValuesIsNull(): void
    {
        $this->arrayList->add(null);
        $this->assertTrue($this->arrayList->containsValue(null));
    }

    public function testCount(): void
    {
        $this->arrayList->add('foo');
        $this->assertEquals(1, $this->arrayList->count());
        $this->arrayList->add('bar');
        $this->assertEquals(2, $this->arrayList->count());
    }

    public function testGetting(): void
    {
        $this->arrayList->add('foo');
        $this->assertEquals('foo', $this->arrayList->get(0));
    }

    public function testGettingIndexGreaterThanListLengthThrowsException(): void
    {
        $this->expectException(OutOfRangeException::class);
        $this->arrayList->add('foo');
        $this->arrayList->get(1);
    }

    public function testGettingIndexLessThanZeroThrowsException(): void
    {
        $this->expectException(OutOfRangeException::class);
        $this->arrayList->add('foo');
        $this->arrayList->get(-1);
    }

    public function testGettingAll(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->add('bar');
        $this->assertEquals(['foo', 'bar'], $this->arrayList->toArray());
    }

    public function testGettingAsArray(): void
    {
        $this->arrayList->add('foo');
        $this->assertEquals('foo', $this->arrayList[0]);
    }

    public function testInsertingValue(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->add('bar');
        $this->arrayList->insert(1, 'baz');
        $this->assertEquals('baz', $this->arrayList->get(1));
        $this->assertEquals('bar', $this->arrayList->get(2));
        $this->assertEquals(['foo', 'baz', 'bar'], $this->arrayList->toArray());
    }

    public function testIntersectingIntersectsValuesOfSetAndArray(): void
    {
        $this->arrayList->addRange(['foo', 'bar']);
        $this->arrayList->intersect(['bar', 'baz']);
        $this->assertEquals(['bar'], $this->arrayList->toArray());
    }

    public function testIteratingOverValues(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->add('bar');
        $actualValues = [];

        foreach ($this->arrayList as $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals(['foo', 'bar'], $actualValues);
    }

    public function testPassingParametersInConstructor(): void
    {
        $parametersArray = ['foo', 'bar'];
        $arrayList = new ArrayList($parametersArray);
        $this->assertEquals($parametersArray, $arrayList->toArray());
    }

    public function testRemove(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->removeValue('foo');
        $this->assertEquals(0, $this->arrayList->count());
        $this->assertFalse($this->arrayList->containsValue('foo'));
        $this->assertEquals([], $this->arrayList->toArray());
    }

    public function testRemoveAt(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->removeIndex(0);
        $this->assertEquals(0, $this->arrayList->count());
        $this->assertFalse($this->arrayList->containsValue('foo'));
        $this->assertEquals([], $this->arrayList->toArray());
    }

    public function testReversing(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->add('bar');
        $this->arrayList->reverse();
        $this->assertEquals(['bar', 'foo'], $this->arrayList->toArray());
    }

    public function testSettingItem(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->add('bar');
        $this->arrayList[1] = 'baz';
        $this->assertEquals('baz', $this->arrayList[1]);
        $this->assertEquals(['foo', 'baz', 'bar'], $this->arrayList->toArray());
    }

    public function testSorting(): void
    {
        $comparer = function ($a, $b) {
            if ($a === 'foo') {
                return 1;
            }

            return -1;
        };
        $this->arrayList->add('foo');
        $this->arrayList->add('bar');
        $this->arrayList->sort($comparer);
        $this->assertEquals(['bar', 'foo'], $this->arrayList->toArray());
    }

    public function testUnionUnionsValuesOfSetAndArray(): void
    {
        $this->arrayList->add('foo');
        $this->arrayList->union(['bar', 'baz']);
        $this->assertEquals(['foo', 'bar', 'baz'], $this->arrayList->toArray());
    }

    public function testUnsetting(): void
    {
        $this->arrayList->add('foo');
        unset($this->arrayList[0]);
        $this->assertEquals(0, $this->arrayList->count());
        $this->assertFalse($this->arrayList->containsValue('foo'));
        $this->assertEquals([], $this->arrayList->toArray());
    }
}
