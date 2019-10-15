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
use PHPUnit\Framework\TestCase;

/**
 * Tests the algorithms
 */
class AlgorithmsTest extends TestCase
{
    public function testCheckingForSupportedAlgorithm(): void
    {
        foreach (Algorithms::getAll() as $algorithm) {
            $this->assertTrue(Algorithms::has($algorithm));
        }
    }

    public function testCheckingForUnsupportedAlgorithm(): void
    {
        $this->assertFalse(Algorithms::has('foo'));
    }

    public function testCheckingIfAlgorithmIsSymmetric(): void
    {
        $this->assertTrue(Algorithms::isSymmetric(Algorithms::SHA256));
        $this->assertTrue(Algorithms::isSymmetric(Algorithms::SHA384));
        $this->assertTrue(Algorithms::isSymmetric(Algorithms::SHA512));
        $this->assertFalse(Algorithms::isSymmetric(Algorithms::RSA_SHA256));
        $this->assertFalse(Algorithms::isSymmetric(Algorithms::RSA_SHA384));
        $this->assertFalse(Algorithms::isSymmetric(Algorithms::RSA_SHA512));
    }

    public function testExceptionThrownOnInvalidAlgorithmSymmetryCheck(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Algorithms::isSymmetric('foo');
    }

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
