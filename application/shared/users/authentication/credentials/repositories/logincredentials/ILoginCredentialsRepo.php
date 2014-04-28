<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for login credentials repos to implement
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Users\Authentication\Credentials;

interface ILoginCredentialsRepo
{
    /**
     * Adds credentials to the repo
     *
     * @param Credentials\ILoginCredentials $credentials The credentials to add to the repo
     * @param string $token The unhashed token
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\ILoginCredentials $credentials, $token = "");

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param int $userId The Id of the user whose credentials these are
     * @param string $hashedToken The hashed token
     * @return bool True if successful, otherwise false
     */
    public function deauthorize($userId, $hashedToken);

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param string $hashedToken The hashed authentication token we are searching for
     * @return Credentials\ILoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndToken($userId, $hashedToken);
} 