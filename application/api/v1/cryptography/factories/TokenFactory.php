<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates a cryptographic token
 */
namespace RamODev\Application\API\V1\Cryptography\Factories;
use RamODev\Application\API\V1\Cryptography;
use RamODev\Application\Configs;

class TokenFactory
{
    /** The number of characters to include in the key */
    const NUM_CHARS = 32;
    /** The lifetime, in seconds, of the key */
    const LIFETIME = 86400;

    /** These are the characters that comprise the key */
    private static $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * Creates a token object
     *
     * @param string $tokenString The token string
     * @param \DateTime $expiration The expiration time for this token
     * @param string $salt The unique salt to use in the HMAC
     * @param string $secretKey The secret key to use in the HMAC
     * @return Cryptography\Token A token object
     */
    public function createToken($tokenString, \DateTime $expiration, $salt, $secretKey)
    {
        return new Cryptography\Token($tokenString, $expiration, $salt, $secretKey);
    }

    /**
     * Generates a new cryptographic token
     *
     * @param string $salt The unique salt to use in the HMAC
     * @param string $secretKey The secret key to use to create the token
     * @return Cryptography\Token A new token object
     */
    public function generateNewToken($salt, $secretKey)
    {
        // Start with the current timestamp to minimize chance of key collision
        $tokenString = time();
        $numPossibleChars = count(self::$chars);

        for($charIter = 0;$charIter < $numPossibleChars;$charIter++)
        {
            $tokenString .= self::$chars[rand(0, $numPossibleChars - 1)];
        }

        $hashedTokenString = hash("sha256", $tokenString);
        $expiration = new \DateTime("+" . self::LIFETIME . " seconds", new \DateTimeZone("UTC"));

        return new Cryptography\Token($hashedTokenString, $expiration, $salt, $secretKey);
    }
} 