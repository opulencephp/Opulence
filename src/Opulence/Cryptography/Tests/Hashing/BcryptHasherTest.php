<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Tests\Hashing;

use Opulence\Cryptography\Hashing\BcryptHasher;

/**
 * Tests the Bcrypt hasher
 */
class BcryptHasherTest extends \PHPUnit\Framework\TestCase
{
    private BcryptHasher $hasher;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->hasher = new BcryptHasher();
    }

    /**
     * Tests getting the default cost
     */
    public function testGettingDefaultCost(): void
    {
        $this->assertEquals(10, BcryptHasher::DEFAULT_COST);
    }

    /**
     * Tests a hash that doesn't need to be rehashed
     */
    public function testHashThatDoesNotNeedToBeRehashed(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 5]);
        $this->assertFalse($this->hasher->needsRehash($hashedValue, ['cost' => 5]));
    }

    /**
     * Tests a hash that needs to be rehashed
     */
    public function testHashThatNeedsToBeRehashed(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 5]);
        $this->assertTrue($this->hasher->needsRehash($hashedValue, ['cost' => 6]));
    }

    /**
     * Tests verifying a correct hash
     */
    public function testVerifyingCorrectHash(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4]);
        $this->assertTrue($this->hasher->verify($hashedValue, 'foo'));
    }

    /**
     * Tests verifying a correct hash with a pepper
     */
    public function testVerifyingCorrectHashWithPepper(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4], 'pepper');
        $this->assertTrue($this->hasher->verify($hashedValue, 'foo', 'pepper'));
    }

    /**
     * Tests verifying an incorrect hash
     */
    public function testVerifyingIncorrectHash(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4]);
        $this->assertFalse($this->hasher->verify($hashedValue, 'bar'));
    }

    /**
     * Tests verifying an incorrect hash with a pepper
     */
    public function testVerifyingIncorrectHashWithPepper(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4], 'pepper');
        $this->assertFalse($this->hasher->verify($hashedValue, 'bar'));
    }
}
