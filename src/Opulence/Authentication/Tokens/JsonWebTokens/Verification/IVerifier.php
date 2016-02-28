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
 * Defines the interface for a JWT verifier to implement
 */
interface IVerifier
{
    /**
     * Verifies a token
     *
     * @param SignedJwt $jwt The token to verify
     * @throws VerificationException Thrown if the token is not valid
     */
    public function verify(SignedJwt $jwt);
}