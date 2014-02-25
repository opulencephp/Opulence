<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token used to authenticate a request
 */
namespace RamODev\API\V1\Cryptography;

class Token
{
    /** @var string The token string */
    private $tokenString = "";
    /** @var \DateTime The expiration of this token */
    private $expiration = null;
    /** @var string The HMAC of the token string and expiration */
    private $hmac = "";

    /**
     * @param string $tokenString The token string
     * @param \DateTime $expiration The expiration time for this token
     * @param string $hmac The HMAC of the token string and expiration
     */
    public function __construct($tokenString, \DateTime $expiration, $hmac)
    {
        $this->tokenString = $tokenString;
        $this->expiration = $expiration;
        $this->hmac = $hmac;
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
    public function getHMAC()
    {
        return $this->hmac;
    }

    /**
     * @return string
     */
    public function getTokenString()
    {
        return $this->tokenString;
    }
} 