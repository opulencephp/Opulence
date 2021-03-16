<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\IssuerVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;

/**
 * Tests the issuer verifier
 */
class IssuerVerifierTest extends \PHPUnit\Framework\TestCase
{
    /** @var IssuerVerifier The verifier to use in tests */
    private $verifier = null;
    /** @var SignedJwt|\PHPUnit_Framework_MockObject_MockObject The token to use in tests */
    private $jwt = null;
    /** @var JwtPayload|\PHPUnit_Framework_MockObject_MockObject The payload to use in tests */
    private $jwtPayload = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->verifier = new IssuerVerifier('foo');
        $this->jwt = $this->getMockBuilder(SignedJwt::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jwtPayload = $this->createMock(JwtPayload::class);
        $this->jwt->expects($this->any())
            ->method('getPayload')
            ->willReturn($this->jwtPayload);
    }

    /**
     * Tests that an invalid issuer
     */
    public function testInvalidIssuer()
    {
        $this->jwtPayload->expects($this->once())
            ->method('getIssuer')
            ->willReturn('bar');
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::ISSUER_INVALID, $error);
    }

    /**
     * Tests verifying valid token
     */
    public function testVerifyingValidToken()
    {
        $this->jwtPayload->expects($this->once())
            ->method('getIssuer')
            ->willReturn('foo');
        $this->assertTrue($this->verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }
}
