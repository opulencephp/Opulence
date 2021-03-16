<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use DateTimeImmutable;
use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;

/**
 * Defines the expiration verifier
 */
class ExpirationVerifier implements IVerifier
{
    /**
     * @inheritdoc
     */
    public function verify(SignedJwt $jwt, string &$error = null) : bool
    {
        $expiration = $jwt->getPayload()->getValidTo();

        if ($expiration === null) {
            return true;
        }

        if ($expiration < new DateTimeImmutable()) {
            $error = JwtErrorTypes::EXPIRED;

            return false;
        }

        return true;
    }
}
