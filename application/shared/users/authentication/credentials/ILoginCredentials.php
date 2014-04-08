<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for login credentials to implement
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials;

interface ILoginCredentials extends ICredentials
{
    /**
     * Gets the expiration time for these credentials
     *
     * @return \DateTime The expiration time
     */
    public function getExpiration();

    /**
     * Gets the hashed authentication token
     *
     * @return string The hashed authentication token
     */
    public function getHashedToken();

    /**
     * Sets the hashed token, which is suitable for storage
     *
     * @param string $hashedToken The hashed token
     */
    public function setHashedToken($hashedToken);
} 