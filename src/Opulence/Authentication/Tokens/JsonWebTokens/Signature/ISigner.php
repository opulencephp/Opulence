<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Signature;

use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;

/**
 * Defines the interface for signers to implement
 */
interface ISigner
{
    /**
     * Gets the algorithm used to sign and verify tokens
     *
     * @return string The algorithm used
     */
    public function getAlgorithm() : string;

    /**
     * Gets the unsigned value
     *
     * @param Jwt $jwt The token whose unsigned value we want
     * @return string The unsigned value
     */
    public function getUnsignedValue(Jwt $jwt) : string;

    /**
     * Signs a token
     *
     * @param Jwt $jwt The token to sign
     */
    public function sign(Jwt $jwt);

    /**
     * Verifies a token
     *
     * @param Jwt $jwt The token to verify
     * @return bool True if the token is valid, otherwise false
     */
    public function verify(Jwt $jwt) : bool;
}