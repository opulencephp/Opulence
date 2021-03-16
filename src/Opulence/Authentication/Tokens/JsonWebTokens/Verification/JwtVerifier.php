<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
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
     * @param array $errors The list of errors, if there are any
     * @return bool True if the token is valid
     */
    public function verify(SignedJwt $jwt, VerificationContext $verificationContext, array &$errors) : bool
    {
        $verifiers = [
            new SignatureVerifier($verificationContext->getSigner()),
            new AudienceVerifier($verificationContext->getAudience()),
            new ExpirationVerifier(),
            new NotBeforeVerifier(),
            new IssuerVerifier($verificationContext->getIssuer()),
            new SubjectVerifier($verificationContext->getSubject())
        ];
        $isVerified = true;

        /** @var IVerifier $verifier */
        foreach ($verifiers as $verifier) {
            $error = '';

            if (!$verifier->verify($jwt, $error)) {
                $isVerified = false;
                $errors[] = $error;
            }
        }

        return $isVerified;
    }
}
