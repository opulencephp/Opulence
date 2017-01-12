<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use DateTimeImmutable;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the not-before verifier
 */
class NotBeforeVerifier implements IVerifier
{
    /**
     * @inheritdoc
     */
    public function verify(SignedJwt $jwt, string &$error = null) : bool
    {
        $notBefore = $jwt->getPayload()->getValidFrom();

        if ($notBefore === null) {
            return true;
        }

        if ($notBefore > new DateTimeImmutable()) {
            $error = JwtErrorTypes::NOT_ACTIVATED;

            return false;
        }

        return true;
    }
}
