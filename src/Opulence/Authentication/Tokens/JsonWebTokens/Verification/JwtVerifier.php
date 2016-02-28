<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the JWT verifier
 */
class JwtVerifier
{
    /**
     * Verifies a token
     *
     * @param SignedJwt $jwt The token to verify
     * @param VerificationContext $verificationContext The context to verify against
     * @return bool True if the token is valid
     * @throws VerificationContext Thrown if the token is not valid
     */
    public function verify(SignedJwt $jwt, VerificationContext $verificationContext) : bool
    {
        $verifiers = [
            new SignatureVerifier($verificationContext->getSigner()),
            new AudienceVerifier($verificationContext->getAudience()),
            new ExpirationVerifier(),
            new NotBeforeVerifier(),
            new IssuerVerifier($verificationContext->getIssuer()),
            new SubjectVerifier($verificationContext->getSubject())
        ];

        /** @var IVerifier $verifier */
        foreach ($verifiers as $verifier) {
            $verifier->verify($jwt);
        }

        return true;
    }
}