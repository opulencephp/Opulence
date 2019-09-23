<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the JWT verifier
 */
final class JwtVerifier implements IContextVerifier
{
    /**
     * @inheritdoc
     */
    public function verify(SignedJwt $jwt, VerificationContext $verificationContext, array &$errors): bool
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
