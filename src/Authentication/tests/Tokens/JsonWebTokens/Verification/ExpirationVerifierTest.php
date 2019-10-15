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

use DateTimeImmutable;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\ExpirationVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the expiration verifier
 */
class ExpirationVerifierTest extends TestCase
{
    private ExpirationVerifier $verifier;
    /** @var SignedJwt|MockObject The token to use in tests */
    private SignedJwt $jwt;
    /** @var JwtPayload|MockObject The payload to use in tests */
    private JwtPayload $jwtPayload;

    protected function setUp(): void
    {
        $this->verifier = new ExpirationVerifier();
        $this->jwt = $this->getMockBuilder(SignedJwt::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jwtPayload = $this->createMock(JwtPayload::class);
        $this->jwt->expects($this->any())
            ->method('getPayload')
            ->willReturn($this->jwtPayload);
    }

    public function testExpiredToken(): void
    {
        $date = new DateTimeImmutable('-30 second');
        $this->jwtPayload->expects($this->once())
            ->method('getValidTo')
            ->willReturn($date);
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::EXPIRED, $error);
    }

    public function testVerifyingValidToken(): void
    {
        $date = new DateTimeImmutable('+30 second');
        $this->jwtPayload->expects($this->once())
            ->method('getValidTo')
            ->willReturn($date);
        $this->assertTrue($this->verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }
}
