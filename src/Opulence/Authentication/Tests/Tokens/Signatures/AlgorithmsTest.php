<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\Signatures;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\Signatures\Algorithms;

/**
 * Tests the algorithms
 */
class AlgorithmsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests checking for a supported algorithm
     */
    public function testCheckingForSupportedAlgorithm(): void
    {
        foreach (Algorithms::getAll() as $algorithm) {
            $this->assertTrue(Algorithms::has($algorithm));
        }
    }

    /**
     * Tests checking for an unsupported algorithm
     */
    public function testCheckingForUnsupportedAlgorithm(): void
    {
        $this->assertFalse(Algorithms::has('foo'));
    }

    /**
     * Tests checking if an algorithm is symmetric
     */
    public function testCheckingIfAlgorithmIsSymmetric(): void
    {
        $this->assertTrue(Algorithms::isSymmetric(Algorithms::SHA256));
        $this->assertTrue(Algorithms::isSymmetric(Algorithms::SHA384));
        $this->assertTrue(Algorithms::isSymmetric(Algorithms::SHA512));
        $this->assertFalse(Algorithms::isSymmetric(Algorithms::RSA_SHA256));
        $this->assertFalse(Algorithms::isSymmetric(Algorithms::RSA_SHA384));
        $this->assertFalse(Algorithms::isSymmetric(Algorithms::RSA_SHA512));
    }

    /**
     * Tests that an exception is thrown when checking if an invalid algorithm is symmetric
     */
    public function testExceptionThrownOnInvalidAlgorithmSymmetryCheck(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Algorithms::isSymmetric('foo');
    }

    /**
     * Tests getting all algorithms
     */
    public function testGettingAllAlgorithms(): void
    {
        $expected = [
            Algorithms::RSA_SHA256,
            Algorithms::RSA_SHA384,
            Algorithms::RSA_SHA512,
            Algorithms::SHA256,
            Algorithms::SHA384,
            Algorithms::SHA512
        ];
        $this->assertEquals($expected, Algorithms::getAll());
    }
}
