<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\TestsTemp\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\AudienceVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the audience verifier
 */
class AudienceVerifierTest extends \PHPUnit\Framework\TestCase
{
    /** @var SignedJwt|MockObject The token to use in tests */
    private SignedJwt $jwt;
    /** @var JwtPayload|MockObject The payload to use in tests */
    private JwtPayload $jwtPayload;

    protected function setUp(): void
    {
        $this->jwt = $this->getMockBuilder(SignedJwt::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jwtPayload = $this->createMock(JwtPayload::class);
        $this->jwt->expects($this->any())
            ->method('getPayload')
            ->willReturn($this->jwtPayload);
    }

    public function testMismatchedAudience(): void
    {
        $verifier = new AudienceVerifier('foo');
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('bar');
        $this->assertFalse($verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::AUDIENCE_INVALID, $error);
    }

    public function testMismatchedAudienceArray(): void
    {
        $verifier = new AudienceVerifier('foo');
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('bar');
        $this->assertFalse($verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::AUDIENCE_INVALID, $error);
    }

    public function testVerifyingEmptyAudienceIsSuccessful(): void
    {
        $verifier = new AudienceVerifier([]);
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('bar');
        $this->assertTrue($verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }

    public function testVerifyingValidArrayAudience(): void
    {
        $verifier = new AudienceVerifier(['foo', 'bar']);
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn(['bar', 'baz']);
        $this->assertTrue($verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }

    public function testVerifyingValidStringAudience(): void
    {
        $verifier = new AudienceVerifier('foo');
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('foo');
        $this->assertTrue($verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }
}
