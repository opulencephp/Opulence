<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\SignatureVerifier;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the signature verifier
 */
class SignatureVerifierTest extends \PHPUnit\Framework\TestCase
{
    /** @var SignatureVerifier The verifier to use in tests */
    private $verifier;
    /** @var ISigner|MockObject The signer to use in tests */
    private $signer;
    /** @var SignedJwt|MockObject The token to use in tests */
    private $jwt;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
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
    public function testEmptySignature(): void
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
    public function testIncorrectSignature(): void
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
    public function testMismatchedAlgorithm(): void
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
    public function testVerifyingValidToken(): void
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
