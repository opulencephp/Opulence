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

use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\IssuerVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the issuer verifier
 */
class IssuerVerifierTest extends TestCase
{
    private IssuerVerifier $verifier;
    /** @var SignedJwt|MockObject The token to use in tests */
    private SignedJwt $jwt;
    /** @var JwtPayload|MockObject The payload to use in tests */
    private JwtPayload $jwtPayload;

    protected function setUp(): void
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

    public function testInvalidIssuer(): void
    {
        $this->jwtPayload->expects($this->once())
            ->method('getIssuer')
            ->willReturn('bar');
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::ISSUER_INVALID, $error);
    }

    public function testVerifyingValidToken(): void
    {
        $this->jwtPayload->expects($this->once())
            ->method('getIssuer')
            ->willReturn('foo');
        $this->assertTrue($this->verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }
}
