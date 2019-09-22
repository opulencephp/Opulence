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
use Opulence\Collections\ImmutableHashTable;
use Opulence\Collections\KeyValuePair;
use Opulence\Collections\Tests\Mocks\MockObject;
use OutOfBoundsException;
use RuntimeException;

/**
 * Tests the immutable hash table
 */
class ImmutableHashTableTest extends \PHPUnit\Framework\TestCase
{
    public function testContainsKey(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', 'bar')]);
        $this->assertFalse($hashTable->containsKey('baz'));
        $this->assertTrue($hashTable->containsKey('foo'));
    }

    public function testContainsKeyReturnsTrueEvenIfValuesIsNull(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', null)]);
        $this->assertTrue($hashTable->containsKey('foo'));
    }

    public function testContainsValue(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', 'bar')]);
        $this->assertFalse($hashTable->containsValue('baz'));
        $this->assertTrue($hashTable->containsValue('bar'));
    }

    public function testCount(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', 'bar'), new KeyValuePair('baz', 'blah')]);
        $this->assertEquals(2, $hashTable->count());
    }

    public function testGetting(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', 'bar')]);
        $this->assertEquals('bar', $hashTable->get('foo'));
    }

    public function testGettingAbsentVariableThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $hashTable = new ImmutableHashTable([]);
        $hashTable->get('does not exist');
    }

    /**
     * Tests that getting the keys returns the original keys, not the hash keys
     */
    public function testGettingKeysReturnsOriginalKeysNotHashKeys(): void
    {
        $kvp1 = new KeyValuePair(new MockObject(), 'foo');
        $kvp2 = new KeyValuePair(new MockObject(), 'bar');
        $hashTable = new ImmutableHashTable([$kvp1, $kvp2]);
        $this->assertEquals([$kvp1->getKey(), $kvp2->getKey()], $hashTable->getKeys());
    }

    public function testGettingValuesReturnsListOfValues(): void
    {
        $kvp1 = new KeyValuePair('foo', 'bar');
        $kvp2 = new KeyValuePair('baz', 'blah');
        $hashTable = new ImmutableHashTable([$kvp1, $kvp2]);
        $this->assertEquals([$kvp1->getKey(), $kvp2->getKey()], $hashTable->getKeys());
    }

    public function testIteratingOverValues(): void
    {
        $expectedArray = [
            new KeyValuePair('foo', 'bar'),
            new KeyValuePair('baz', 'blah')
        ];
        $hashTable = new ImmutableHashTable($expectedArray);
        $actualValues = [];

        foreach ($hashTable as $key => $value) {
            // Make sure the hash keys aren't returned by the iterator
            $this->assertTrue(is_int($key));
            $actualValues[] = $value;
        }

        $this->assertEquals($expectedArray, $actualValues);
    }

    /**
     * Tests that a non-key-value pair in the constructor throws an exception
     */
    public function testNonKeyValuePairInConstructorThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ImmutableHashTable(['foo' => 'bar']);
    }

    public function testSettingValueThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $hashTable = new ImmutableHashTable([]);
        $hashTable['foo'] = 'bar';
    }

    public function testToArray(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', 'bar'), new KeyValuePair('baz', 'blah')]);
        $expectedArray = [
            new KeyValuePair('foo', 'bar'),
            new KeyValuePair('baz', 'blah')
        ];
        $this->assertEquals($expectedArray, $hashTable->toArray());
    }

    public function testTryGetReturnsTrueIfKeyExistsAndFalseIfValueDoesNotExist(): void
    {
        $hashTable = new ImmutableHashTable([new KeyValuePair('foo', 'bar')]);
        $value = null;
        $this->assertFalse($hashTable->tryGet('baz', $value));
        $this->assertNull($value);
        $this->assertTrue($hashTable->tryGet('foo', $value));
        $this->assertEquals('bar', $value);
    }

    public function testUnsettingValueThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $hashTable = new ImmutableHashTable([]);
        unset($hashTable['foo']);
    }
}
