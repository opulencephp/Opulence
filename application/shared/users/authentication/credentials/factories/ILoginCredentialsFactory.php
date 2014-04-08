<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for login credentials factories to implement
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Factories;
use RamODev\Application\Shared\Users\Authentication\Credentials;

interface ILoginCredentialsFactory
{
    /**
     * Creates credentials for the input user
     *
     * @param int $userID The ID of the user whose credentials these are
     * @param \DateTime $expiration The expiration time
     * @return Credentials\ILoginCredentials
     */
    public function createLoginCredentials($userID, \DateTime $expiration);
} 