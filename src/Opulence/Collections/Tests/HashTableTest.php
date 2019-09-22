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

use InvalidArgumentException;
use Opulence\Collections\HashTable;
use Opulence\Collections\KeyValuePair;
use Opulence\Collections\Tests\Mocks\MockObject;
use OutOfBoundsException;

/**
 * Tests the hash table
 */
class HashTableTest extends \PHPUnit\Framework\TestCase
{
    /** @var HashTable The hash table to use in tests */
    private $hashTable;

    protected function setUp(): void
    {
        $this->hashTable = new HashTable();
    }

    public function testAddingRangeMakesEachValueRetrievable(): void
    {
        $this->hashTable->addRange([new KeyValuePair('foo', 'bar'), new KeyValuePair('baz', 'blah')]);
        $this->assertEquals('bar', $this->hashTable->get('foo'));
        $this->assertEquals('blah', $this->hashTable->get('baz'));
    }

    public function testAddingValueMakesItRetrievable(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals('bar', $this->hashTable->get('foo'));
    }

    public function testCheckingOffsetExists(): void
    {
        $this->hashTable['foo'] = 'bar';
        $this->assertTrue(isset($this->hashTable['foo']));
    }

    public function testClearing(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->clear();
        $this->assertEquals([], $this->hashTable->toArray());
    }

    public function testContainsKey(): void
    {
        $this->assertFalse($this->hashTable->containsKey('foo'));
        $this->hashTable->add('foo', 'bar');
        $this->assertTrue($this->hashTable->containsKey('foo'));
    }

    public function testContainsKeyReturnsTrueEvenIfValuesIsNull(): void
    {
        $this->hashTable->add('foo', null);
        $this->assertTrue($this->hashTable->containsKey('foo'));
    }

    public function testContainsValue(): void
    {
        $this->assertFalse($this->hashTable->containsValue('bar'));
        $this->hashTable->add('foo', 'bar');
        $this->assertTrue($this->hashTable->containsValue('bar'));
    }

    public function testCount(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals(1, $this->hashTable->count());
        $this->hashTable->add('bar', 'foo');
        $this->assertEquals(2, $this->hashTable->count());
    }

    public function testGetting(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals('bar', $this->hashTable->get('foo'));
    }

    public function testGettingAbsentVariableThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->hashTable->get('does not exist');
    }

    public function testGettingAsArray(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals('bar', $this->hashTable['foo']);
    }

    /**
     * Tests that getting the keys returns the original keys, not the hash keys
     */
    public function testGettingKeysReturnsOriginalKeysNotHashKeys(): void
    {
        $key1 = new MockObject();
        $key2 = new MockObject();
        $this->hashTable->add($key1, 'foo');
        $this->hashTable->add($key2, 'bar');
        $this->assertEquals([$key1, $key2], $this->hashTable->getKeys());
    }

    public function testGettingValuesReturnsListOfValues(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->add('baz', 'blah');
        $this->assertEquals(['bar', 'blah'], $this->hashTable->getValues());
    }

    public function testIteratingOverValues(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->add('baz', 'blah');
        $expectedArray = [
            new KeyValuePair('foo', 'bar'),
            new KeyValuePair('baz', 'blah')
        ];
        $actualValues = [];

        foreach ($this->hashTable as $key => $value) {
            // Make sure the hash keys aren't returned by the iterator
            $this->assertTrue(is_int($key));
            $actualValues[] = $value;
        }

        $this->assertEquals($expectedArray, $actualValues);
    }

    /**
     * Tests that a non-key-value pair in the add-range method throws an exception
     */
    public function testNonKeyValuePairInAddRangeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->hashTable->addRange(['foo' => 'bar']);
    }

    /**
     * Tests that a non-key-value pair in the constructor throws an exception
     */
    public function testNonKeyValuePairInConstructorThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new HashTable(['foo' => 'bar']);
    }

    public function testPassingParametersInConstructor(): void
    {
        $hashTable = new HashTable([new KeyValuePair('foo', 'bar'), new KeyValuePair('baz', 'blah')]);
        $this->assertEquals('bar', $hashTable->get('foo'));
        $this->assertEquals('blah', $hashTable->get('baz'));
    }

    public function testRemoveKey(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->removeKey('foo');
        $this->assertFalse($this->hashTable->containsKey('foo'));
    }

    public function testSettingItem(): void
    {
        $this->hashTable['foo'] = 'bar';
        $this->assertEquals('bar', $this->hashTable['foo']);
    }

    public function testToArray(): void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->add('baz', 'blah');
        $expectedArray = [
            new KeyValuePair('foo', 'bar'),
            new KeyValuePair('baz', 'blah')
        ];
        $this->assertEquals($expectedArray, $this->hashTable->toArray());
    }

    public function testTryGetReturnsTrueIfKeyExistsAndFalseIfValueDoesNotExist(): void
    {
        $value = null;
        $this->assertFalse($this->hashTable->tryGet('foo', $value));
        $this->assertNull($value);
        $this->hashTable->add('foo', 'bar');
        $this->assertTrue($this->hashTable->tryGet('foo', $value));
        $this->assertEquals('bar', $value);
    }

    public function testUnsetting(): void
    {
        $this->hashTable['foo'] = 'bar';
        unset($this->hashTable['foo']);
        $this->assertFalse($this->hashTable->containsKey('foo'));
    }
}
