<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;


use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the interface for verifiers that use the context to do the verification
 */
interface IContextVerifier
{
    /**
     * Verifies a token
     *
     * @param SignedJwt $jwt The token to verify
     * @param VerificationContext $verificationContext The context to verify against
     * @param array $errors The list of errors, if there are any
     * @return bool True if the token is valid
     */
    public function verify(SignedJwt $jwt, VerificationContext $verificationContext, array &$errors): bool;
}
