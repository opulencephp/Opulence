<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Signature;

/**
 * Tests the JWS algorithms
 */
class JwsAlgorithmsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests checking for a supported algorithm
     */
    public function testCheckingForSupportedAlgorithm()
    {
        foreach (JwsAlgorithms::getAll() as $algorithm) {
            $this->assertTrue(JwsAlgorithms::has($algorithm));
        }
    }

    /**
     * Tests checking for an unsupported algorithm
     */
    public function testCheckingForUnsupportedAlgorithm()
    {
        $this->assertFalse(JwsAlgorithms::has("foo"));
    }

    /**
     * Tests getting all algorithms
     */
    public function testGettingAllAlgorithms()
    {
        $expected = [
            JwsAlgorithms::RSA_SHA256,
            JwsAlgorithms::RSA_SHA384,
            JwsAlgorithms::RSA_SHA512,
            JwsAlgorithms::SHA256,
            JwsAlgorithms::SHA384,
            JwsAlgorithms::SHA512
        ];
        $this->assertEquals($expected, JwsAlgorithms::getAll());
    }
}