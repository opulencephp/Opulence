<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Factories;

use DateInterval;
use DateTimeImmutable;
use Opulence\Authentication\Credentials\Credential;
use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Tokens\JsonWebTokens\UnsignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Defines the JWT credential factory
 */
abstract class JwtCredentialFactory
{
    /** @var ISigner The token signer */
    protected $signer = null;
    /** @var string The issuer of the JWT */
    protected $issuer = "";
    /** @var array|string The issuer of the JWT */
    protected $audience = "";
    /** @var DateInterval The interval from the moment of creation that the JWT is valid from */
    protected $validFromInterval = null;
    /** @var DateInterval The interval from the moment of creation that the JWT is valid to */
    protected $validToInterval = null;

    /**
     * @param ISigner $signer The token signer
     * @param string $issuer The issuer of the JWT
     * @param array|string $audience The audience of the JWT
     * @param DateInterval $validFromInterval The interval from the moment of creation that the JWT is valid from
     * @param DateInterval $validToInterval The interval from the moment of creation that the JWT is valid to
     */
    public function __construct(
        ISigner $signer,
        string $issuer,
        $audience,
        DateInterval $validFromInterval,
        DateInterval $validToInterval
    ) {
        $this->signer = $signer;
        $this->issuer = $issuer;
        $this->audience = $audience;
        $this->validFromInterval = $validFromInterval;
        $this->validToInterval = $validToInterval;
    }

    /**
     * @inheritdoc
     */
    public function createCredentialForSubject(ISubject $subject) : ICredential
    {
        $jwt = $this->getSignedJwt($subject);

        return new Credential($this->getCredentialType(), ["token" => $jwt->encode()]);
    }

    /**
     * Gets the credential type
     *
     * @return string The credential type
     */
    abstract protected function getCredentialType() : string;

    /**
     * Adds any custom claims to the payload
     *
     * @param JwtPayload $payload The current payload
     * @param ISubject $subject The subject whose credential we're creating
     */
    protected function addCustomClaims(JwtPayload $payload, ISubject $subject)
    {
        // Let extending classes define this
    }

    /**
     * Gets the signed JWT for a subject
     *
     * @param ISubject $subject The subject whose credential we're creating
     * @return SignedJwt The signed JWT
     */
    protected function getSignedJwt(ISubject $subject) : SignedJwt
    {
        $jwtPayload = new JwtPayload();
        $jwtPayload->setIssuer($this->issuer);
        $jwtPayload->setAudience($this->audience);
        $jwtPayload->setSubject($subject->getPrimaryPrincipal()->getId());
        $jwtPayload->setValidFrom((new DateTimeImmutable)->add($this->validFromInterval));
        $jwtPayload->setValidTo((new DateTimeImmutable)->add($this->validToInterval));
        $jwtPayload->setIssuedAt(new DateTimeImmutable());
        $this->addCustomClaims($jwtPayload, $subject);
        $unsignedJwt = new UnsignedJwt(new JwtHeader(), $jwtPayload);

        return SignedJwt::createFromUnsignedJwt($unsignedJwt, $this->signer->sign($unsignedJwt->getUnsignedValue()));
    }
}
