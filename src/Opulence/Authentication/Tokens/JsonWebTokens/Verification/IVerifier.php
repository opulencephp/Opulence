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
 * Defines the interface for a JWT verifier to implement
 */
interface IVerifier
{
    /**
     * Verifies a token
     *
     * @param SignedJwt $jwt The token to verify
     * @param string $error The error type, if there was one
     * @return bool True if the token is valid, otherwise false
     */
    public function verify(SignedJwt $jwt, string &$error = null): bool;
}
