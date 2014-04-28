<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login credentials factory
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Factories;
use RamODev\Application\Shared\Users\Authentication\Credentials;

class LoginCredentialsFactory implements ILoginCredentialsFactory
{
    /**
     * Creates credentials for the input user
     *
     * @param int $userId The Id of the user whose credentials these are
     * @param \DateTime $expiration The expiration time
     * @return Credentials\LoginCredentials
     */
    public function createLoginCredentials($userId, \DateTime $expiration)
    {
        return new Credentials\LoginCredentials($userId, "", $expiration);
    }
} 