<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\SignatureVerifier;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Tests the signature verifier
 */
class SignatureVerifierTest extends \PHPUnit\Framework\TestCase
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
    public function setUp() : void
    {
        $this->signer = $this->createMock(ISigner::class);
        $this->verifier = new SignatureVerifier($this->signer);
        $this->jwt = $this->getMockBuilder(SignedJwt::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests an empty signature
     */
    public function testEmptySignature()
    {
        $this->jwt->expects($this->once())
            ->method('getSignature')
            ->willReturn('');
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::SIGNATURE_INCORRECT, $error);
    }

    /**
     * Tests an incorrect signature
     */
    public function testIncorrectSignature()
    {
        $this->jwt->expects($this->once())
            ->method('getSignature')
            ->willReturn('foo');
        $this->signer->expects($this->once())
            ->method('verify')
            ->willReturn(false);
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::SIGNATURE_INCORRECT, $error);
    }

    /**
     * Tests mismatched algorithm
     */
    public function testMismatchedAlgorithm()
    {
        $this->jwt->expects($this->once())
            ->method('getSignature')
            ->willReturn('foo');
        $header = $this->createMock(JwtHeader::class);
        $header->expects($this->once())
            ->method('getAlgorithm')
            ->willReturn(Algorithms::RSA_SHA384);
        $this->jwt->expects($this->once())
            ->method('getHeader')
            ->willReturn($header);
        $this->jwt->expects($this->once())
            ->method('getSignature')
            ->willReturn('foo');
        $this->signer->expects($this->once())
            ->method('getAlgorithm')
            ->willReturn(Algorithms::SHA384);
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::SIGNATURE_ALGORITHM_MISMATCH, $error);
    }

    /**
     * Tests verifying valid token
     */
    public function testVerifyingValidToken()
    {
        $this->jwt->expects($this->once())
            ->method('getSignature')
            ->willReturn('foo');
        $this->signer->expects($this->once())
            ->method('verify')
            ->willReturn(true);
        $this->assertTrue($this->verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }
}
