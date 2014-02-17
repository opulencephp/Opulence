<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a token used to authenticate a request
 */
namespace RamODev\API\V1\Authentication\Tokens;

class Token
{
    /** @var string The token string */
    private $tokenString = "";
    /** @var \DateTime The expiration of this token */
    private $expiration = null;

    /**
     * @param string $tokenString The token string
     * @param \DateTime $expiration The expiration time for this token
     */
    public function __construct($tokenString, \DateTime $expiration)
    {
        $this->tokenString = $tokenString;
        $this->expiration = $expiration;
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
    public function getTokenString()
    {
        return $this->tokenString;
    }

    /**
     * @param \DateTime $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }

    /**
     * @param string $tokenString
     */
    public function setTokenString($tokenString)
    {
        $this->tokenString = $tokenString;
    }
} 