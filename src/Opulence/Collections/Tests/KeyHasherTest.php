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

use Opulence\Collections\KeyHasher;
use Opulence\Collections\Tests\Mocks\SerializableObject;
use Opulence\Collections\Tests\Mocks\UnserializableObject;

/**
 * Tests the key hasher
 */
class KeyHasherTest extends \PHPUnit\Framework\TestCase
{
    /** @var KeyHasher The hasher to use in tests */
    private $keyHasher;

    protected function setUp(): void
    {
        $this->keyHasher = new KeyHasher();
    }

    public function testArraysAreHashedToCorrectKey(): void
    {
        $array = ['foo'];
        $this->assertEquals('__opulence:a:' . md5(serialize($array)), $this->keyHasher->getHashKey($array));
    }

    public function testScalarsAreHashedToCorrectKey(): void
    {
        $this->assertEquals('__opulence:s:1', $this->keyHasher->getHashKey('1'));
        $this->assertEquals('__opulence:i:1', $this->keyHasher->getHashKey(1));
        $this->assertEquals('__opulence:f:1.1', $this->keyHasher->getHashKey(1.1));
    }

    public function testResourceIsHashedUsingItsStringValue(): void
    {
        $resource = fopen('php://temp', 'r+b');
        $this->assertEquals("__opulence:r:$resource", $this->keyHasher->getHashKey($resource));
    }

    public function testSerializableObjectIsHashedWithToStringMethod(): void
    {
        $object = new SerializableObject('foo');
        $this->assertEquals('__opulence:so:foo', $this->keyHasher->getHashKey($object));
    }

    public function testUnserializableObjectIsHashedWithObjectHash(): void
    {
        $object = new UnserializableObject();
        $this->assertEquals('__opulence:o:' . spl_object_hash($object), $this->keyHasher->getHashKey($object));
    }
}
