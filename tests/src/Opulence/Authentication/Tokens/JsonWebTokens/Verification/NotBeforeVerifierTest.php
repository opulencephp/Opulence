<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use DateTimeImmutable;
use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;

/**
 * Tests the not-before verifier
 */
class NotBeforeVerifierTest extends \PHPUnit_Framework_TestCase
{
    /** @var NotBeforeVerifier The verifier to use in tests */
    private $verifier = null;
    /** @var Jwt|\PHPUnit_Framework_MockObject_MockObject The token to use in tests */
    private $jwt = null;
    /** @var JwtPayload|\PHPUnit_Framework_MockObject_MockObject The payload to use in tests */
    private $jwtPayload = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->verifier = new NotBeforeVerifier();
        $this->jwt = $this->getMock(Jwt::class, [], [], "", false);
        $this->jwtPayload = $this->getMock(JwtPayload::class);
        $this->jwt->expects($this->any())
            ->method("getPayload")
            ->willReturn($this->jwtPayload);
    }

    /**
     * Tests that an exception is thrown on an expired token
     */
    public function testExceptionThrownOnInvalidToken()
    {
        $this->setExpectedException(VerificationException::class);
        $date = new DateTimeImmutable("+30 second");
        $this->jwtPayload->expects($this->once())
            ->method("getValidFrom")
            ->willReturn($date);
        $this->verifier->verify($this->jwt);
    }

    /**
     * Tests verifying valid token
     */
    public function testVerifyingValidToken()
    {
        $date = new DateTimeImmutable("-30 second");
        $this->jwtPayload->expects($this->once())
            ->method("getValidFrom")
            ->willReturn($date);
        $this->verifier->verify($this->jwt);
    }
}