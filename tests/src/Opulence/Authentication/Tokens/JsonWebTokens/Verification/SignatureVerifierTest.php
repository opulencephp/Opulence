<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Tests the signature verifier
 */
class SignatureVerifierTest extends \PHPUnit_Framework_TestCase
{
    /** @var SignatureVerifier The verifier to use in tests */
    private $verifier = null;
    /** @var ISigner|\PHPUnit_Framework_MockObject_MockObject The signer to use in tests */
    private $signer = null;
    /** @var SignedJwt|\PHPUnit_Framework_MockObject_MockObject The token to use in tests */
    private $jwt = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->signer = $this->getMock(ISigner::class);
        $this->verifier = new SignatureVerifier($this->signer);
        $this->jwt = $this->getMock(SignedJwt::class, [], [], "", false);
    }

    /**
     * Tests that an exception is thrown on an empty signature
     */
    public function testExceptionThrownOnEmptySignature()
    {
        $this->setExpectedException(VerificationException::class);
        $this->jwt->expects($this->once())
            ->method("getSignature")
            ->willReturn("");
        $this->verifier->verify($this->jwt);
    }

    /**
     * Tests that an exception is thrown on an invalid token
     */
    public function testExceptionThrownOnInvalidToken()
    {
        $this->setExpectedException(VerificationException::class);
        $this->jwt->expects($this->once())
            ->method("getSignature")
            ->willReturn("foo");
        $this->signer->expects($this->once())
            ->method("verify")
            ->willReturn(false);
        $this->verifier->verify($this->jwt);
    }

    /**
     * Tests that an exception is thrown when signer's algorithm does not match token's
     */
    public function testExceptionThrownWhenSignersAlgorithmDoesNotMatchTokens()
    {
        $this->setExpectedException(VerificationException::class);
        $this->jwt->expects($this->once())
            ->method("getSignature")
            ->willReturn("foo");
        $header = $this->getMock(JwtHeader::class);
        $header->expects($this->once())
            ->method("getAlgorithm")
            ->willReturn(Algorithms::RSA_SHA384);
        $this->jwt->expects($this->once())
            ->method("getHeader")
            ->willReturn($header);
        $this->jwt->expects($this->once())
            ->method("getSignature")
            ->willReturn("foo");
        $this->signer->expects($this->once())
            ->method("getAlgorithm")
            ->willReturn(Algorithms::RSA_SHA384);
        $this->verifier->verify($this->jwt);
    }

    /**
     * Tests verifying valid token
     */
    public function testVerifyingValidToken()
    {
        $this->jwt->expects($this->once())
            ->method("getSignature")
            ->willReturn("foo");
        $this->signer->expects($this->once())
            ->method("verify")
            ->willReturn(true);
        $this->verifier->verify($this->jwt);
    }
}