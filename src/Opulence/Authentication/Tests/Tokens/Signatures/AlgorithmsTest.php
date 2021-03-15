<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

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
    public function testCheckingForSupportedAlgorithm()
    {
        foreach (Algorithms::getAll() as $algorithm) {
            $this->assertTrue(Algorithms::has($algorithm));
        }
    }

    /**
     * Tests checking for an unsupported algorithm
     */
    public function testCheckingForUnsupportedAlgorithm()
    {
        $this->assertFalse(Algorithms::has('foo'));
    }

    /**
     * Tests checking if an algorithm is symmetric
     */
    public function testCheckingIfAlgorithmIsSymmetric()
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
    public function testExceptionThrownOnInvalidAlgorithmSymmetryCheck()
    {
        $this->expectException(InvalidArgumentException::class);
        Algorithms::isSymmetric('foo');
    }

    /**
     * Tests getting all algorithms
     */
    public function testGettingAllAlgorithms()
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
