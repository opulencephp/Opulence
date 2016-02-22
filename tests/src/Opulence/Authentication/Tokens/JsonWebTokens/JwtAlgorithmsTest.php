<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens;

/**
 * Tests the JWT algorithms
 */
class JwtAlgorithmsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests checking for a supported algorithm
     */
    public function testCheckingForSupportedAlgorithm()
    {
        foreach (JwtAlgorithms::getAll() as $algorithm) {
            $this->assertTrue(JwtAlgorithms::has($algorithm));
        }
    }

    /**
     * Tests checking for an unsupported algorithm
     */
    public function testCheckingForUnsupportedAlgorithm()
    {
        $this->assertFalse(JwtAlgorithms::has("foo"));
    }

    /**
     * Tests getting all algorithms
     */
    public function testGettingAllAlgorithms()
    {
        $expected = [
            JwtAlgorithms::RSA_SHA256,
            JwtAlgorithms::RSA_SHA384,
            JwtAlgorithms::RSA_SHA512,
            JwtAlgorithms::SHA256,
            JwtAlgorithms::SHA384,
            JwtAlgorithms::SHA512
        ];
        $this->assertEquals($expected, JwtAlgorithms::getAll());
    }
}