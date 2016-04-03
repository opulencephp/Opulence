<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Authenticators;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\UnsignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtVerifier;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\HmacSigner;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Tests the JWT authenticator
 */
class JwtAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var JwtAuthenticator The authenticator to use in tests */
    private $authenticator = null;
    /** @var JwtVerifier|\PHPUnit_Framework_MockObject_MockObject The JWT verifier to use in tests */
    private $jwtVerifier = null;
    /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject The credential to use in tests */
    private $credential = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        /** @var ISigner $signer */
        $signer = $this->getMock(ISigner::class);
        $this->jwtVerifier = $this->getMock(JwtVerifier::class, ["verify"]);
        $this->authenticator = new JwtAuthenticator($signer, $this->jwtVerifier);
        $this->credential = $this->getMock(ICredential::class);

        // Set up the signed JWT
        $signer = new HmacSigner(Algorithms::SHA256, "public");
        $unsignedJwt = new UnsignedJwt(new JwtHeader(Algorithms::SHA256), new JwtPayload());
        $unsignedJwt->getPayload()->setSubject("Dave");
        $signature = $signer->sign($unsignedJwt->getUnsignedValue());
        $signedJwt = SignedJwt::createFromUnsignedJwt($unsignedJwt, $signature);
        $tokenString = $signedJwt->encode();
        $this->credential->expects($this->any())
            ->method("getValue")
            ->with("token")
            ->willReturn($tokenString);
    }

    /**
     * Tests that an unset token credential will return false
     */
    public function testUnsetTokenCredentialReturnsFalse()
    {
        $credential = $this->getMock(ICredential::class);
        $credential->expects($this->any())
            ->method("getValue")
            ->with("token")
            ->willReturn(null);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_MISSING, $error);
    }

    /**
     * Tests that an unverified JWT returns false
     */
    public function testUnverifiedJwtReturnsFalse()
    {
        $this->jwtVerifier
            ->expects($this->any())
            ->method("verify")
            ->willReturn(false);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_INCORRECT, $error);
    }

    /**
     * Tests that a verified JWT returns true
     */
    public function testVerifiedJwtReturnsTrue()
    {
        $this->jwtVerifier
            ->expects($this->any())
            ->method("verify")
            ->willReturn(true);
        $subject = null;
        $this->assertTrue($this->authenticator->authenticate($this->credential, $subject));
        /** @var ISubject $subject */
        $this->assertInstanceOf(ISubject::class, $subject);
        $this->assertEquals("Dave", $subject->getPrimaryPrincipal()->getId());
        $this->assertEquals([$this->credential], $subject->getCredentials());
    }
}