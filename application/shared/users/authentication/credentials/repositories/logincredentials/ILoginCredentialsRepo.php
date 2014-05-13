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
     * @param Credentials\LoginCredentials $credentials The credentials to add to the repo
     * @param string $hashedLoginTokenValue The hashed login token value
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\LoginCredentials $credentials, $hashedLoginTokenValue);

    /**
     * Deactivates the input credentials from the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Credentials\LoginCredentials $credentials);

    /**
     * Deactivates all the login credentials for a user
     * This is useful in such cases like password changes where we want to deactivate all old sessions
     *
     * @param int $userId The Id of the user whose credentials we are deactivating
     * @return bool True if successful, otherwise false
     */
    public function deactivateAllByUserId($userId);

    /**
     * Gets a list of all the login credentials for a user
     *
     * @param int $userId The Id of the user whose login credentials we want
     * @return array|bool The list of login credentials if successful, otherwise false
     */
    public function getAllByUserId($userId);

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we're searching for
     * @param string $unhashedLoginTokenValue The unhashed login token we are searching for
     * @return Credentials\LoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndLoginToken($userId, $loginTokenId, $unhashedLoginTokenValue);
} 