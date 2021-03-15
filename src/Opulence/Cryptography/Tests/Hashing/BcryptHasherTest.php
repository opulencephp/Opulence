<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Cryptography\Tests\Hashing;

use Opulence\Cryptography\Hashing\BcryptHasher;

/**
 * Tests the Bcrypt hasher
 */
class BcryptHasherTest extends \PHPUnit\Framework\TestCase
{
    /** @var BcryptHasher The hasher to use in the tests */
    private $hasher = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->hasher = new BcryptHasher();
    }

    /**
     * Tests getting the default cost
     */
    public function testGettingDefaultCost()
    {
        $this->assertEquals(10, BcryptHasher::DEFAULT_COST);
    }

    /**
     * Tests a hash that doesn't need to be rehashed
     */
    public function testHashThatDoesNotNeedToBeRehashed()
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 5]);
        $this->assertFalse($this->hasher->needsRehash($hashedValue, ['cost' => 5]));
    }

    /**
     * Tests a hash that needs to be rehashed
     */
    public function testHashThatNeedsToBeRehashed()
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 5]);
        $this->assertTrue($this->hasher->needsRehash($hashedValue, ['cost' => 6]));
    }

    /**
     * Tests verifying a correct hash
     */
    public function testVerifyingCorrectHash()
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4]);
        $this->assertTrue(BcryptHasher::verify($hashedValue, 'foo'));
    }

    /**
     * Tests verifying a correct hash with a pepper
     */
    public function testVerifyingCorrectHashWithPepper()
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4], 'pepper');
        $this->assertTrue(BcryptHasher::verify($hashedValue, 'foo', 'pepper'));
    }

    /**
     * Tests verifying an incorrect hash
     */
    public function testVerifyingIncorrectHash()
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4]);
        $this->assertFalse(BcryptHasher::verify($hashedValue, 'bar'));
    }

    /**
     * Tests verifying an incorrect hash with a pepper
     */
    public function testVerifyingIncorrectHashWithPepper()
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4], 'pepper');
        $this->assertFalse(BcryptHasher::verify($hashedValue, 'bar'));
    }
}
