<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\ReadOnlyHashTable;

/**
 * Tests the read-only hash table
 */
class ReadOnlyHashTableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests whether the hash table has a certain key
     */
    public function testContainsKey() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => 'bar']);
        $this->assertFalse($hashTable->containsKey('baz'));
        $this->assertTrue($hashTable->containsKey('foo'));
    }

    /**
     * Tests that checking if a key exists returns true even if the value is null
     */
    public function testContainsKeyReturnsTrueEvenIfValuesIsNull() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => null]);
        $this->assertTrue($hashTable->containsKey('foo'));
    }

    /**
     * Tests whether the hash table has a certain value
     */
    public function testContainsValue() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => 'bar']);
        $this->assertFalse($hashTable->containsValue('baz'));
        $this->assertTrue($hashTable->containsValue('bar'));
    }

    /**
     * Tests counting
     */
    public function testCount() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => 'bar', 'baz' => 'blah']);
        $this->assertEquals(2, $hashTable->count());
    }

    /**
     * Tests getting a value
     */
    public function testGetting() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => 'bar']);
        $this->assertEquals('bar', $hashTable->get('foo'));
    }

    /**
     * Tests getting an absent variable with a default
     */
    public function testGettingAbsentVariableWithDefault() : void
    {
        $hashTable = new ReadOnlyHashTable([]);
        $this->assertEquals('blah', $hashTable->get('does not exist', 'blah'));
    }

    /**
     * Tests getting an absent variable with no default
     */
    public function testGettingAbsentVariableWithNoDefault() : void
    {
        $hashTable = new ReadOnlyHashTable([]);
        $this->assertNull($hashTable->get('does not exist'));
    }

    /**
     * Tests iterating over the values
     */
    public function testIteratingOverValues() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => 'bar', 'baz' => 'blah']);
        $actualValues = [];

        foreach ($hashTable as $key => $value) {
            $actualValues[$key] = $value;
        }

        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $actualValues);
    }

    /**
     * Tests getting as array
     */
    public function testToArray() : void
    {
        $hashTable = new ReadOnlyHashTable(['foo' => 'bar', 'baz' => 'blah']);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $hashTable->toArray());
    }
}
