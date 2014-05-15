<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login credentials factory
 */
namespace RDev\Application\Shared\Models\Users\Authentication\Credentials\Factories;
use RDev\Application\Shared\Models\Cryptography;
use RDev\Application\Shared\Models\Users\Authentication\Credentials;

class LoginCredentialsFactory
{
    /**
     * Creates credentials for the input user
     *
     * @param int $userId The Id of the user whose credentials these are
     * @param \DateTime $validFrom The valid-from time
     * @param \DateTime $validTo The valid-to time
     * @return Credentials\LoginCredentials
     */
    public function createLoginCredentials($userId, \DateTime $validFrom, \DateTime $validTo)
    {
        return new Credentials\LoginCredentials($userId,
            new Cryptography\Token(-1, Cryptography\TokenTypes::LOGIN, $userId, $validFrom, $validTo, true));
    }
} 