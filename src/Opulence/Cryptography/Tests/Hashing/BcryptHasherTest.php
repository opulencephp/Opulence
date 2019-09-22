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

    protected function setUp(): void
    {
        $this->hasher = new BcryptHasher();
    }

    public function testGettingDefaultCost(): void
    {
        $this->assertEquals(10, BcryptHasher::DEFAULT_COST);
    }

    public function testHashThatDoesNotNeedToBeRehashed(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 5]);
        $this->assertFalse($this->hasher->needsRehash($hashedValue, ['cost' => 5]));
    }

    public function testHashThatNeedsToBeRehashed(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 5]);
        $this->assertTrue($this->hasher->needsRehash($hashedValue, ['cost' => 6]));
    }

    public function testVerifyingCorrectHash(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4]);
        $this->assertTrue($this->hasher->verify($hashedValue, 'foo'));
    }

    public function testVerifyingCorrectHashWithPepper(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4], 'pepper');
        $this->assertTrue($this->hasher->verify($hashedValue, 'foo', 'pepper'));
    }

    public function testVerifyingIncorrectHash(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4]);
        $this->assertFalse($this->hasher->verify($hashedValue, 'bar'));
    }

    public function testVerifyingIncorrectHashWithPepper(): void
    {
        $hashedValue = $this->hasher->hash('foo', ['cost' => 4], 'pepper');
        $this->assertFalse($this->hasher->verify($hashedValue, 'bar'));
    }
}
