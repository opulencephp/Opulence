<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Utilities;

/**
 * Defines some string utilities
 *
 * @deprecated since 1.0.0-beta3
 */
class Strings
{
    /**
     * Creates a cryptographically-strong random bytes
     *
     * @param int $length The desired number of bytes
     * @return string The random bytes
     */
    public function generateRandomBytes(int $length) : string
    {
        return random_bytes($length);
    }

    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     */
    public function generateRandomString(int $length) : string
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = bin2hex($this->generateRandomBytes(ceil($length / 2)));

        if ($length % 2 == 1) {
            // Slice off one character to make it the appropriate odd length
            $string = \mb_substr($string, 1, null, "8bit");
        }

        return $string;
    }

    /**
     * Creates a cryptographically-strong UUID version 4
     *
     * @return string The UUID
     */
    public function generateUuidV4() : string
    {
        $string = $this->generateRandomBytes(16);
        $string[6] = chr(ord($string[6]) & 0x0f | 0x40);
        $string[8] = chr(ord($string[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($string), 4));
    }

    /**
     * Checks if two strings are equal without having to worry about timing attacks
     *
     * @param string $knownString The known string
     * @param string $userString The user string
     * @return bool True if the strings are equal, otherwise false
     */
    public function isEqual(string $knownString, string $userString) : bool
    {
        return \hash_equals($knownString, $userString);
    }
}