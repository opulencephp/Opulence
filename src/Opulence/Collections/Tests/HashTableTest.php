<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

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
    private $hashTable = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->hashTable = new HashTable();
    }

    /**
     * Tests adding multiple values make each one retrievable
     */
    public function testAddingRangeMakesEachValueRetrievable() : void
    {
        $this->hashTable->addRange([new KeyValuePair('foo', 'bar'), new KeyValuePair('baz', 'blah')]);
        $this->assertEquals('bar', $this->hashTable->get('foo'));
        $this->assertEquals('blah', $this->hashTable->get('baz'));
    }

    /**
     * Tests adding a value makes it retrievable
     */
    public function testAddingValueMakesItRetrievable() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals('bar', $this->hashTable->get('foo'));
    }

    /**
     * Tests checking if an offset exists
     */
    public function testCheckingOffsetExists() : void
    {
        $this->hashTable['foo'] = 'bar';
        $this->assertTrue(isset($this->hashTable['foo']));
    }

    /**
     * Tests clearing
     */
    public function testClearing() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->clear();
        $this->assertEquals([], $this->hashTable->toArray());
    }

    /**
     * Tests whether the hash table has a certain key
     */
    public function testContainsKey() : void
    {
        $this->assertFalse($this->hashTable->containsKey('foo'));
        $this->hashTable->add('foo', 'bar');
        $this->assertTrue($this->hashTable->containsKey('foo'));
    }

    /**
     * Tests that checking if a key exists returns true even if the value is null
     */
    public function testContainsKeyReturnsTrueEvenIfValuesIsNull() : void
    {
        $this->hashTable->add('foo', null);
        $this->assertTrue($this->hashTable->containsKey('foo'));
    }

    /**
     * Tests whether the hash table has a certain value
     */
    public function testContainsValue() : void
    {
        $this->assertFalse($this->hashTable->containsValue('bar'));
        $this->hashTable->add('foo', 'bar');
        $this->assertTrue($this->hashTable->containsValue('bar'));
    }

    /**
     * Tests counting
     */
    public function testCount() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals(1, $this->hashTable->count());
        $this->hashTable->add('bar', 'foo');
        $this->assertEquals(2, $this->hashTable->count());
    }

    /**
     * Tests getting a parameter
     */
    public function testGetting() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals('bar', $this->hashTable->get('foo'));
    }

    /**
     * Tests getting an absent variable throws an exception
     */
    public function testGettingAbsentVariableThrowsException() : void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->hashTable->get('does not exist');
    }

    /**
     * Tests getting as array
     */
    public function testGettingAsArray() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->assertEquals('bar', $this->hashTable['foo']);
    }

    /**
     * Tests that getting the keys returns the original keys, not the hash keys
     */
    public function testGettingKeysReturnsOriginalKeysNotHashKeys() : void
    {
        $key1 = new MockObject();
        $key2 = new MockObject();
        $this->hashTable->add($key1, 'foo');
        $this->hashTable->add($key2, 'bar');
        $this->assertEquals([$key1, $key2], $this->hashTable->getKeys());
    }

    /**
     * Tests that getting the values returns a list of values
     */
    public function testGettingValuesReturnsListOfValues() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->add('baz', 'blah');
        $this->assertEquals(['bar', 'blah'], $this->hashTable->getValues());
    }

    /**
     * Tests iterating over the values
     */
    public function testIteratingOverValues() : void
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
    public function testNonKeyValuePairInAddRangeThrowsException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->hashTable->addRange(['foo' => 'bar']);
    }

    /**
     * Tests that a non-key-value pair in the constructor throws an exception
     */
    public function testNonKeyValuePairInConstructorThrowsException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new HashTable(['foo' => 'bar']);
    }

    /**
     * Tests passing parameters through the constructor
     */
    public function testPassingParametersInConstructor() : void
    {
        $hashTable = new HashTable([new KeyValuePair('foo', 'bar'), new KeyValuePair('baz', 'blah')]);
        $this->assertEquals('bar', $hashTable->get('foo'));
        $this->assertEquals('blah', $hashTable->get('baz'));
    }

    /**
     * Tests removing a key
     */
    public function testRemoveKey() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->removeKey('foo');
        $this->assertFalse($this->hashTable->containsKey('foo'));
    }

    /**
     * Tests setting an item
     */
    public function testSettingItem() : void
    {
        $this->hashTable['foo'] = 'bar';
        $this->assertEquals('bar', $this->hashTable['foo']);
    }

    /**
     * Tests converting to an array
     */
    public function testToArray() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->add('baz', 'blah');
        $expectedArray = [
            new KeyValuePair('foo', 'bar'),
            new KeyValuePair('baz', 'blah')
        ];
        $this->assertEquals($expectedArray, $this->hashTable->toArray());
    }

    /**
     * Tests the trying to get a value returns true if the key exists and false if it doesn't
     */
    public function testTryGetReturnsTrueIfKeyExistsAndFalseIfValueDoesNotExist() : void
    {
        $value = null;
        $this->assertFalse($this->hashTable->tryGet('foo', $value));
        $this->assertNull($value);
        $this->hashTable->add('foo', 'bar');
        $this->assertTrue($this->hashTable->tryGet('foo', $value));
        $this->assertEquals('bar', $value);
    }

    /**
     * Tests unsetting a parameter
     */
    public function testUnsetting() : void
    {
        $this->hashTable['foo'] = 'bar';
        unset($this->hashTable['foo']);
        $this->assertFalse($this->hashTable->containsKey('foo'));
    }
}
