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

/**
 * Tests the expiration verifier
 */
class ExpirationVerifierTest extends \PHPUnit\Framework\TestCase
{
    /** @var ExpirationVerifier The verifier to use in tests */
    private $verifier;
    /** @var SignedJwt|MockObject The token to use in tests */
    private $jwt;
    /** @var JwtPayload|MockObject The payload to use in tests */
    private $jwtPayload;

    /**
     * Sets up the tests
     */
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

    /**
     * Tests an expired token
     */
    public function testExpiredToken(): void
    {
        $date = new DateTimeImmutable('-30 second');
        $this->jwtPayload->expects($this->once())
            ->method('getValidTo')
            ->willReturn($date);
        $this->assertFalse($this->verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::EXPIRED, $error);
    }

    /**
     * Tests verifying valid token
     */
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
