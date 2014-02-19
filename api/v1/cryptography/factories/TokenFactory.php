<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates a token
 */
namespace RamODev\V1\Cryptography\Factories\Token;
use RamODev\API\V1\Cryptography;

class Factory
{
    /** The number of characters to include in the key */
    const NUM_CHARS = 32;
    /** The lifetime, in seconds, of the key */
    const LIFETIME = 86400;

    /** These are the characters that comprise the key */
    private static $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * Generates a cryptographic token
     *
     * @return Cryptography\Token A new token
     */
    public function createCryptographicToken()
    {
        // Start with the current timestamp to minimize chance of key collision
        $tokenString = time();
        $numPossibleChars = count(self::$chars);

        for($charIter = 0;$charIter < $numPossibleChars;$charIter++)
        {
            $tokenString .= self::$chars[rand(0, $numPossibleChars - 1)];
        }

        $hashedTokenString = hash("sha256", $tokenString);
        // Set the expiration to some time far in the future
        $expiration = new \DateTime("now", new \DateTimeZone("UTC"));
        $expiration->setTimestamp(time() + self::LIFETIME);

        return new Tokens\Token($hashedTokenString, $expiration);
    }
} 