<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;

/**
 * Tests the subject verifier
 */
class SubjectVerifierTest extends \PHPUnit_Framework_TestCase
{
    /** @var SubjectVerifier The verifier to use in tests */
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
        $this->verifier = new SubjectVerifier("foo");
        $this->jwt = $this->getMock(Jwt::class, [], [], "", false);
        $this->jwtPayload = $this->getMock(JwtPayload::class);
        $this->jwt->expects($this->any())
            ->method("getPayload")
            ->willReturn($this->jwtPayload);
    }

    /**
     * Tests that an exception is thrown on an invalid token
     */
    public function testExceptionThrownOnInvalidToken()
    {
        $this->setExpectedException(VerificationException::class);
        $this->jwtPayload->expects($this->once())
            ->method("getSubject")
            ->willReturn("bar");
        $this->verifier->verify($this->jwt);
    }

    /**
     * Tests verifying valid token
     */
    public function testVerifyingValidToken()
    {
        $this->jwtPayload->expects($this->once())
            ->method("getSubject")
            ->willReturn("foo");
        $this->verifier->verify($this->jwt);
    }
}