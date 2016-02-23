<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\Signature\ISigner;
use Opulence\Authentication\Tokens\JsonWebTokens\Signature\JwsAlgorithms;

/**
 * Tests the JWT verifier
 */
class JwtVerifierTest extends \PHPUnit_Framework_TestCase
{
    /** @var JwtVerifier The verifier to use in tests */
    private $verifier = null;
    /** @var ISigner The signer to use in tests */
    private $signer = null;
    /** @var VerificationContext The verification context to use in tests */
    private $context = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->verifier = new JwtVerifier();
        $this->signer = $this->getMock(ISigner::class);
        $this->signer->expects($this->any())
            ->method("getAlgorithm")
            ->willReturn(JwsAlgorithms::SHA256);
        $this->signer->expects($this->any())
            ->method("verify")
            ->willReturn(true);
        $this->context = new VerificationContext($this->signer);
    }

    /**
     * Tests verifying a valid token
     */
    public function testVerifyingValidToken()
    {
        $jwt = new Jwt(new JwtHeader(), new JwtPayload());
        $jwt->setSignature("signature");
        $this->assertTrue($this->verifier->verify($jwt, $this->context));
    }
}