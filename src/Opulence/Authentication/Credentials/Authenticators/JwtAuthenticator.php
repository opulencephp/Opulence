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

/**
 * Defines the JWT authenticator
 */
class JwtAuthenticator implements IAuthenticator
{
    /** @var JwtVerifier The JWT verifier */
    protected $jwtVerifier = null;
    /** @var VerificationContext The verification context to use */
    protected $verificationContext = null;

    /**
     * @param JwtVerifier $jwtVerifier The JWT verifier
     * @param VerificationContext $verificationContext The verification context to use
     */
    public function __construct(JwtVerifier $jwtVerifier, VerificationContext $verificationContext)
    {
        $this->jwtVerifier = $jwtVerifier;
        $this->verificationContext = $verificationContext;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(ICredential $credential, ISubject &$subject = null, string &$error = null) : bool
    {
        $tokenString = $credential->getValue("token");

        if ($tokenString === null) {
            $error = AuthenticatorErrorTypes::CREDENTIAL_MISSING;

            return false;
        }

        $jwt = SignedJwt::createFromString($tokenString);
        $errors = [];

        if (!$this->jwtVerifier->verify($jwt, $this->verificationContext, $errors)) {
            $error = AuthenticatorErrorTypes::CREDENTIAL_INCORRECT;

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