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
use Opulence\Authentication\Principal;
use Opulence\Authentication\PrincipalTypes;
use Opulence\Authentication\Subject;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\JwtVerifier;
use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Defines the JWT authenticator
 */
class JwtAuthenticator implements IAuthenticator
{
    /** @var ISigner The token signer */
    protected $signer = null;
    /** @var JwtVerifier The JWT verifier */
    protected $jwtVerifier = null;

    /**
     * @param ISigner $signer The token signer
     * @param JwtVerifier $jwtVerifier The JWT verifier
     */
    public function __construct(ISigner $signer, JwtVerifier $jwtVerifier)
    {
        $this->signer = $signer;
        $this->jwtVerifier = $jwtVerifier;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, ISubject &$subject = null) : bool
    {
        $tokenString = $credential->getValue("token");

        if ($tokenString === null) {
            return false;
        }

        $jwt = SignedJwt::createFromString($tokenString);
        $verificationContext = new VerificationContext($this->signer);
        $errors = [];

        if (!$this->jwtVerifier->verify($jwt, $verificationContext, $errors)) {
            return false;
        }

        $subject = $this->getSubjectFromJwt($jwt, $credential);

        return true;
    }

    /**
     * Gets a subject from a JWT
     *
     * @param SignedJwt $jwt The signed JWT
     * @param ICredential $credential The credential
     * @return ISubject The subject
     */
    protected function getSubjectFromJwt(SignedJwt $jwt, ICredential $credential) : ISubject
    {
        $roles = $jwt->getPayload()->get("roles") ?: [];

        return new Subject(
            [new Principal(PrincipalTypes::PRIMARY, $jwt->getPayload()->getSubject(), $roles)],
            [$credential]
        );
    }
}