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
    public function testHashThatDoesNotNeedToBeRehashed(): void
    {
        $hasher = new BcryptHasher(['cost' => 5]);
        $hashedValue = $hasher->hash('foo');
        $this->assertFalse($hasher->needsRehash($hashedValue));
    }

    public function testHashThatNeedsToBeRehashed(): void
    {
        $hasher = new BcryptHasher(['cost' => 5]);
        $hashedValue = $hasher->hash('foo');
        $this->assertTrue($hasher->needsRehash($hashedValue));
    }

    public function testVerifyingCorrectHash(): void
    {
        $hasher = new BcryptHasher(['cost' => 4]);
        $hashedValue = $hasher->hash('foo');
        $this->assertTrue($hasher->verify($hashedValue, 'foo'));
    }

    public function testVerifyingCorrectHashWithPepper(): void
    {
        $hasher = new BcryptHasher(['cost' => 4]);
        $hashedValue = $hasher->hash('foo', 'pepper');
        $this->assertTrue($hasher->verify($hashedValue, 'foo', 'pepper'));
    }

    public function testVerifyingIncorrectHash(): void
    {
        $hasher = new BcryptHasher(['cost' => 4]);
        $hashedValue = $hasher->hash('foo');
        $this->assertFalse($hasher->verify($hashedValue, 'bar'));
    }

    public function testVerifyingIncorrectHashWithPepper(): void
    {
        $hasher = new BcryptHasher(['cost' => 4]);
        $hashedValue = $hasher->hash('foo', 'pepper');
        $this->assertFalse($hasher->verify($hashedValue, 'bar'));
    }
}
