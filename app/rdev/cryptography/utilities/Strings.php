<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines some string utilities
 */
namespace RDev\Cryptography\Utilities;
use RDev\Cryptography;
use Symfony\Component\Security\Core\Util;

class Strings
{
    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     * @throws Cryptography\CryptographicException Thrown if there was an error generating the hash
     */
    public function generateRandomString($length)
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = bin2hex(openssl_random_pseudo_bytes(ceil($length / 2), $isStrong));

        if($string === false || !$isStrong)
        {
            throw new Cryptography\CryptographicException("Generated hash was not secure");
        }

        if($length % 2 == 1)
        {
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
        return Util\StringUtils::equals($knownString, $userString);
    }
}