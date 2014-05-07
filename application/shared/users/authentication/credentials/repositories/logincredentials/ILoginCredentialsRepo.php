<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for login credential repos to implement
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Users\Authentication\Credentials;

interface ILoginCredentialsRepo
{
    /**
     * Adds credentials to the repo
     *
     * @param Credentials\ILoginCredentials $credentials The credentials to add to the repo
     * @param string $hashedLoginTokenValue The hashed login token value
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\ILoginCredentials $credentials, $hashedLoginTokenValue);

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param Credentials\ILoginCredentials $credentials The credentials to deauthorize
     * @param string $unhashedLoginTokenValue The unhashed token value
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Credentials\ILoginCredentials $credentials, $unhashedLoginTokenValue);

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we're searching for
     * @param string $unhashedLoginTokenValue The unhashed login token we are searching for
     * @return Credentials\ILoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndLoginToken($userId, $loginTokenId, $unhashedLoginTokenValue);
} 