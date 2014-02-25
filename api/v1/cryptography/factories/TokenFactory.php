<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates a cryptographic token
 */
namespace RamODev\API\V1\Cryptography\Factories;
use RamODev\API\V1\Cryptography;
use RamODev\Configs;

require_once(__DIR__ . "/../../../../configs/AuthenticationConfig.php");

class TokenFactory
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
     * @param string $publicKey The secret key to use to create the token
     * @return Cryptography\Token A new token
     */
    public function createCryptographicToken($publicKey)
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
        $expiration = new \DateTime(null, new \DateTimeZone("UTC"));
        $expiration->setTimestamp(time() + self::LIFETIME);

        return new Cryptography\Token($hashedTokenString, $expiration, hash_hmac("sha256", Configs\AuthenticationConfig::TOKEN_PRIVATE_KEY . $hashedTokenString . $expiration->getTimestamp(), $publicKey));
    }
} 