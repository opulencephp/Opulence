<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Credentials\Authenticators;

use Opulence\Authentication\Credentials\Authenticators\AuthenticatorErrorTypes;
use Opulence\Authentication\Credentials\Authenticators\RefreshTokenAuthenticator;
use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\Orm\IJwtRepository;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\UnsignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\HmacSigner;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the refresh token authenticator
 */
class RefreshTokenAuthenticatorTest extends \PHPUnit\Framework\TestCase
{
    /** @var RefreshTokenAuthenticator The authenticator to use in tests */
    private $authenticator;
    /** @var JwtVerifier|MockObject The JWT verifier to use in tests */
    private $jwtVerifier;
    /** @var ICredential|MockObject The credential to use in tests */
    private $credential;
    /** @var IJwtRepository|MockObject The refresh token repository */
    private $refreshTokenRepository;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->refreshTokenRepository = $this->createMock(IJwtRepository::class);
        /** @var ISigner $signer */
        $signer = $this->createMock(ISigner::class);
        $verificationContext = new VerificationContext($signer);
        $this->jwtVerifier = $this->getMockBuilder(JwtVerifier::class)
            ->setMethods(['verify'])
            ->getMock();
        $this->authenticator = new RefreshTokenAuthenticator(
            $this->refreshTokenRepository,
            $this->jwtVerifier,
            $verificationContext
        );
        $this->credential = $this->createMock(ICredential::class);

        // Set up the signed JWT
        $signer = new HmacSigner(Algorithms::SHA256, 'public');
        $unsignedJwt = new UnsignedJwt(new JwtHeader(Algorithms::SHA256), new JwtPayload());
        $unsignedJwt->getPayload()->setSubject('Dave');
        $signature = $signer->sign($unsignedJwt->getUnsignedValue());
        $signedJwt = SignedJwt::createFromUnsignedJwt($unsignedJwt, $signature);
        $tokenString = $signedJwt->encode();
        $this->credential->expects($this->any())
            ->method('getValue')
            ->with('token')
            ->willReturn($tokenString);
    }

    /**
     * Tests that an unset token credential will return false
     */
    public function testUnsetTokenCredentialReturnsFalse(): void
    {
        /** @var ICredential|MockObject $credential */
        $credential = $this->createMock(ICredential::class);
        $credential->expects($this->any())
            ->method('getValue')
            ->with('token')
            ->willReturn(null);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_MISSING, $error);
    }

    /**
     * Tests that an unverified JWT returns false
     */
    public function testUnverifiedJwtReturnsFalse(): void
    {
        $this->jwtVerifier
            ->expects($this->any())
            ->method('verify')
            ->willReturn(false);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_INCORRECT, $error);
    }

    /**
     * Tests that a verified JWT that does not exist in the repository returns false
     */
    public function testVerifiedJwtThatDoesNotExistInRepositoryReturnsFalse(): void
    {
        $this->jwtVerifier
            ->expects($this->any())
            ->method('verify')
            ->willReturn(true);
        $this->refreshTokenRepository->expects($this->any())
            ->method('has')
            ->willReturn(false);
        $subject = null;
        $error = null;
        $this->assertFalse($this->authenticator->authenticate($this->credential, $subject, $error));
        $this->assertEquals(AuthenticatorErrorTypes::CREDENTIAL_INCORRECT, $error);
    }

    /**
     * Tests that a verified JWT that exists in the repository returns true
     */
    public function testVerifiedJwtThatExistsInRepositoryReturnsTrue(): void
    {
        $this->jwtVerifier
            ->expects($this->any())
            ->method('verify')
            ->willReturn(true);
        $this->refreshTokenRepository->expects($this->any())
            ->method('has')
            ->willReturn(true);
        $subject = null;
        $this->assertTrue($this->authenticator->authenticate($this->credential, $subject));
        /** @var ISubject $subject */
        $this->assertInstanceOf(ISubject::class, $subject);
        $this->assertEquals('Dave', $subject->getPrimaryPrincipal()->getId());
        $this->assertEquals([$this->credential], $subject->getCredentials());
    }
}
