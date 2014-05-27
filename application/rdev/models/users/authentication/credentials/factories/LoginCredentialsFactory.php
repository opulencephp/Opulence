<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login credentials factory
 */
namespace RDev\Models\Users\Authentication\Credentials\Factories;
use RDev\Models\Cryptography;
use RDev\Models\Users\Authentication\Credentials;

class LoginCredentialsFactory
{
    /**
     * Creates credentials for the input user
     *
     * @param int $userId The Id of the user whose credentials these are
     * @param Cryptography\Token $loginToken The login token
     * @return Credentials\LoginCredentials The credentials for the user
     */
    public function createLoginCredentials($userId, Cryptography\Token $loginToken)
    {
        return new Credentials\LoginCredentials($userId, $loginToken);
    }
} 