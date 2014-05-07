<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token factory
 */
namespace RamODev\Application\Shared\Cryptography\Factories;
use RamODev\Application\Shared\Cryptography;

class TokenFactory
{
    /**
     * Creates a cryptographically-strong random string
     *
     * @param int $length The desired length of the string
     * @return string The random string
     */
    public function createRandomString($length)
    {
        // N bytes becomes 2N characters in bin2hex(), hence the division by 2
        $string = bin2hex(openssl_random_pseudo_bytes(ceil($length / 2)));

        if($length % 2 == 1)
        {
            // Slice off one character to make it the appropriate odd length
            $string = substr($string, 1);
        }

        return $string;
    }

    /**
     * Creates a cryptographic token
     *
     * @param \DateTime $validFrom The valid-from date
     * @param \DateTime $validTo The valid-to date
     * @return Cryptography\Token The token
     */
    public function createToken($validFrom, $validTo)
    {
        return new Cryptography\Token(-1, $validFrom, $validTo);
    }
} 