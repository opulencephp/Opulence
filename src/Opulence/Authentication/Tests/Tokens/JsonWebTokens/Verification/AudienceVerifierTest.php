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
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\AudienceVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtErrorTypes;

/**
 * Tests the audience verifier
 */
class AudienceVerifierTest extends \PHPUnit\Framework\TestCase
{
    /** @var SignedJwt|\PHPUnit_Framework_MockObject_MockObject The token to use in tests */
    private $jwt = null;
    /** @var JwtPayload|\PHPUnit_Framework_MockObject_MockObject The payload to use in tests */
    private $jwtPayload = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->jwt = $this->getMockBuilder(SignedJwt::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jwtPayload = $this->createMock(JwtPayload::class);
        $this->jwt->expects($this->any())
            ->method('getPayload')
            ->willReturn($this->jwtPayload);
    }

    /**
     * Tests a mismatched audience
     */
    public function testMismatchedAudience()
    {
        $verifier = new AudienceVerifier('foo');
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('bar');
        $this->assertFalse($verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::AUDIENCE_INVALID, $error);
    }

    /**
     * Tests a mismatched array audience
     */
    public function testMismatchedAudienceArray()
    {
        $verifier = new AudienceVerifier('foo');
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('bar');
        $this->assertFalse($verifier->verify($this->jwt, $error));
        $this->assertEquals(JwtErrorTypes::AUDIENCE_INVALID, $error);
    }

    /**
     * Tests verifying against an empty audience is successful
     */
    public function testVerifyingEmptyAudienceIsSuccessful()
    {
        $verifier = new AudienceVerifier([]);
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('bar');
        $this->assertTrue($verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }

    /**
     * Tests verifying valid array audience
     */
    public function testVerifyingValidArrayAudience()
    {
        $verifier = new AudienceVerifier(['foo', 'bar']);
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn(['bar', 'baz']);
        $this->assertTrue($verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }

    /**
     * Tests verifying valid string audience
     */
    public function testVerifyingValidStringAudience()
    {
        $verifier = new AudienceVerifier('foo');
        $this->jwtPayload->expects($this->once())
            ->method('getAudience')
            ->willReturn('foo');
        $this->assertTrue($verifier->verify($this->jwt, $error));
        $this->assertNull($error);
    }
}
