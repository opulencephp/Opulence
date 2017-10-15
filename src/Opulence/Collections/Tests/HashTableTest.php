<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\HashTable;
use Opulence\Collections\KeyValuePair;

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
        $this->hashTable->addRange(['foo' => 'bar', 'baz' => 'blah']);
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
     * Tests getting an absent variable with a default
     */
    public function testGettingAbsentVariableWithDefault() : void
    {
        $this->assertEquals('blah', $this->hashTable->get('does not exist', 'blah'));
    }

    /**
     * Tests getting an absent variable with no default
     */
    public function testGettingAbsentVariableWithNoDefault() : void
    {
        $this->assertNull($this->hashTable->get('does not exist'));
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
     * Tests passing parameters through the constructor
     */
    public function testPassingParametersInConstructor() : void
    {
        $parametersArray = ['foo' => 'bar', 'bar' => 'foo'];
        $hashTable = new HashTable($parametersArray);
        $this->assertEquals('bar', $hashTable->get('foo'));
        $this->assertEquals('foo', $hashTable->get('bar'));
    }

    /**
     * Tests removing a parameter
     */
    public function testRemove() : void
    {
        $this->hashTable->add('foo', 'bar');
        $this->hashTable->removeKey('foo');
        $this->assertNull($this->hashTable->get('foo'));
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
     * Tests unsetting a parameter
     */
    public function testUnsetting() : void
    {
        $this->hashTable['foo'] = 'bar';
        unset($this->hashTable['foo']);
        $this->assertNull($this->hashTable->get('foo'));
    }
}
