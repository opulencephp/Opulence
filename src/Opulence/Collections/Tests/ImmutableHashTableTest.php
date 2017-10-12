<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\ImmutableHashTable;
use RuntimeException;

/**
 * Tests the immutable hash table
 */
class ImmutableHashTableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests whether the hash table has a certain key
     */
    public function testContainsKey() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => 'bar']);
        $this->assertFalse($hashTable->containsKey('baz'));
        $this->assertTrue($hashTable->containsKey('foo'));
    }

    /**
     * Tests that checking if a key exists returns true even if the value is null
     */
    public function testContainsKeyReturnsTrueEvenIfValuesIsNull() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => null]);
        $this->assertTrue($hashTable->containsKey('foo'));
    }

    /**
     * Tests whether the hash table has a certain value
     */
    public function testContainsValue() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => 'bar']);
        $this->assertFalse($hashTable->containsValue('baz'));
        $this->assertTrue($hashTable->containsValue('bar'));
    }

    /**
     * Tests counting
     */
    public function testCount() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => 'bar', 'baz' => 'blah']);
        $this->assertEquals(2, $hashTable->count());
    }

    /**
     * Tests getting a value
     */
    public function testGetting() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => 'bar']);
        $this->assertEquals('bar', $hashTable->get('foo'));
    }

    /**
     * Tests getting an absent variable with a default
     */
    public function testGettingAbsentVariableWithDefault() : void
    {
        $hashTable = new ImmutableHashTable([]);
        $this->assertEquals('blah', $hashTable->get('does not exist', 'blah'));
    }

    /**
     * Tests getting an absent variable with no default
     */
    public function testGettingAbsentVariableWithNoDefault() : void
    {
        $hashTable = new ImmutableHashTable([]);
        $this->assertNull($hashTable->get('does not exist'));
    }

    /**
     * Tests iterating over the values
     */
    public function testIteratingOverValues() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => 'bar', 'baz' => 'blah']);
        $actualValues = [];

        foreach ($hashTable as $key => $value) {
            $actualValues[$key] = $value;
        }

        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $actualValues);
    }

    /**
     * Tests that setting a value throws an exception
     */
    public function testSettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $hashTable = new ImmutableHashTable([]);
        $hashTable['foo'] = 'bar';
    }

    /**
     * Tests getting as array
     */
    public function testToArray() : void
    {
        $hashTable = new ImmutableHashTable(['foo' => 'bar', 'baz' => 'blah']);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $hashTable->toArray());
    }

    /**
     * Tests that unsetting a value throws an exception
     */
    public function testUnsettingValueThrowsException() : void
    {
        $this->expectException(RuntimeException::class);
        $hashTable = new ImmutableHashTable([]);
        unset($hashTable['foo']);
    }
}
