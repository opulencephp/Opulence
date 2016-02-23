<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Signature;

/**
 * Defines the various algorithms that can be used by JSON web tokens
 */
class JwsAlgorithms
{
    /** The RSA SHA256 algorithm */
    const RSA_SHA256 = "RS256";
    /** The RSA SHA384 algorithm */
    const RSA_SHA384 = "RS384";
    /** The RSA SHA512 algorithm */
    const RSA_SHA512 = "RS512";
    /** The SHA256 algorithm */
    const SHA256 = "HS256";
    /** The SHA384 algorithm */
    const SHA384 = "HS384";
    /** The SHA512 algorithm */
    const SHA512 = "HS512";

    /**
     * Gets all the supported algorithms
     *
     * @return array All the supported algorithms
     */
    public static function getAll() : array
    {
        return [
            self::RSA_SHA256,
            self::RSA_SHA384,
            self::RSA_SHA512,
            self::SHA256,
            self::SHA384,
            self::SHA512
        ];
    }

    /**
     * Gets whether or not an algorithm is supported by JWT
     *
     * @param mixed $algorithm The algorithm to search
     * @return bool True if the algorithm is supported, otherwise false
     */
    public static function has($algorithm) : bool
    {
        return in_array($algorithm, self::getAll());
    }
}