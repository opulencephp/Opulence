<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token used to authenticate a request
 */
namespace RamODev\Application\API\V1\Cryptography;
use RamODev\Application\Configs;

class Token
{
    /** The hashing algorithm to use */
    const HASH_ALGORITHM = "sha256";

    /** @var string The token string */
    private $tokenString = "";
    /** @var \DateTime The expiration of this token */
    private $expiration = null;
    /** @var string The unique salt to use in the HMAC */
    private $salt = "";
    /** @var string The secret key to use in the HMAC */
    private $secretKey = "";
    /** @var string The HMAC of the token string and expiration */
    private $hmac = "";

    /**
     * @param string $tokenString The token string
     * @param \DateTime $expiration The expiration time for this token
     * @param string $salt The unique salt to use in the HMAC
     * @param string $secretKey The secret key to use in the HMAC
     */
    public function __construct($tokenString, \DateTime $expiration, $salt, $secretKey)
    {
        $this->tokenString = $tokenString;
        $this->expiration = $expiration;
        $this->salt = $salt;
        $this->secretKey = $secretKey;
        $this->hmac = hash_hmac(self::HASH_ALGORITHM, Configs\AuthenticationConfig::GLOBAL_HMAC_SALT . $this->salt . $this->tokenString . $this->expiration->getTimestamp(), $this->secretKey);
    }

    /**
     * @return \DateTime
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @return string
     */
    public function getTokenString()
    {
        return $this->tokenString;
    }

    /**
     * Validates an HMAC
     *
     * @param string $hmac The HMAC to validate
     * @return bool True if the input HMAC is valid, otherwise false
     */
    public function hmacIsValid($hmac)
    {
        return $this->hmac === $hmac;
    }

    /**
     * Gets whether or not this token is expired
     *
     * @return bool True if this token is expired, otherwise false
     */
    public function isExpired()
    {
        return $this->expiration <= new \DateTime("now", new \DateTimeZone("UTC"));
    }
} 