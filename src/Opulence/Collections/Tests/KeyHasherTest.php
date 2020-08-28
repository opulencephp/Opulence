<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\KeyHasher;
use Opulence\Collections\Tests\Mocks\SerializableObject;
use Opulence\Collections\Tests\Mocks\UnserializableObject;

/**
 * Tests the key hasher
 */
class KeyHasherTest extends \PHPUnit\Framework\TestCase
{
    /** @var KeyHasher The hasher to use in tests */
    private $keyHasher = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->keyHasher = new KeyHasher();
    }

    /**
     * Tests that arrays are hashed to the correct key
     */
    public function testArraysAreHashedToCorrectKey() : void
    {
        $array = ['foo'];
        $this->assertSame('__opulence:a:' . md5(serialize($array)), $this->keyHasher->getHashKey($array));
    }

    /**
     * Tests that scalars are hashed to the correct key
     */
    public function testScalarsAreHashedToCorrectKey() : void
    {
        $this->assertSame('__opulence:s:1', $this->keyHasher->getHashKey('1'));
        $this->assertSame('__opulence:i:1', $this->keyHasher->getHashKey(1));
        $this->assertSame('__opulence:f:1.1', $this->keyHasher->getHashKey(1.1));
    }

    /**
     * Tests that a resource is hashes using its string value
     */
    public function testResourceIsHashedUsingItsStringValue() : void
    {
        $resource = fopen('php://temp', 'r+');
        $this->assertSame("__opulence:r:$resource", $this->keyHasher->getHashKey($resource));
    }

    /**
     * Tests that a serializable object is hashed with its __toString() method
     */
    public function testSerializableObjectIsHashedWithToStringMethod() : void
    {
        $object = new SerializableObject('foo');
        $this->assertSame('__opulence:so:foo', $this->keyHasher->getHashKey($object));
    }

    /**
     * Tests that an unserializable object is hashed with object hash
     */
    public function testUnserializableObjectIsHashedWithObjectHash() : void
    {
        $object = new UnserializableObject();
        $this->assertSame('__opulence:o:' . spl_object_hash($object), $this->keyHasher->getHashKey($object));
    }
}
