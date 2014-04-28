<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a user's login credentials
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials;

class LoginCredentials implements ILoginCredentials
{
    /** @var int The Id of the user whose credentials these are */
    private $userId = -1;
    /** @var string The hashed authentication token */
    private $hashedToken = "";
    /** @var \DateTime The expiration of these credentials */
    private $expiration = null;

    /**
     * @param int $userId The Id of the user whose credentials these are
     * @param string $hashedToken The hashed authentication token
     * @param \DateTime $expiration The expiration of these credentials
     */
    public function __construct($userId, $hashedToken, \DateTime $expiration)
    {
        $this->userId = $userId;
        $this->hashedToken = $hashedToken;
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
     * Gets the hashed authentication token
     *
     * @return string The hashed authentication token
     */
    public function getHashedToken()
    {
        return $this->hashedToken;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $hashedToken
     */
    public function setHashedToken($hashedToken)
    {
        $this->hashedToken = $hashedToken;
    }
} 