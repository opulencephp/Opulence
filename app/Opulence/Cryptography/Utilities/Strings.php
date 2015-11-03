<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Utilities;

use Opulence\Cryptography\CryptographicException;
use Symfony\Component\Security\Core\Util\StringUtils;

/**
 * Defines some string utilities
 */
class Strings
{
    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     * @throws CryptographicException Thrown if there was an error generating the hash
     */
    public function generateRandomString($length)
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = bin2hex(openssl_random_pseudo_bytes(ceil($length / 2), $isStrong));

        if ($string === false || !$isStrong) {
            throw new CryptographicException("Generated hash was not secure");
        }

        if ($length % 2 == 1) {
            // Slice off one character to make it the appropriate odd length
            $string = mb_substr($string, 1);
        }

        return $string;
    }

    /**
     * Checks if two strings are equal without having to worry about timing attacks
     *
     * @param string $knownString The known string
     * @param string $userString The user string
     * @return bool True if the strings are equal, otherwise false
     */
    public function isEqual($knownString, $userString)
    {
        return StringUtils::equals($knownString, $userString);
    }
}