<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from a PostgreSQL database
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements ILoginCredentialsRepo
{
    /** @var Factories\ILoginCredentialsFactory The factory to use when creating login credentials */
    private $loginCredentialsFactory = null;

    /**
     * @param SQL\Database $sqlDatabase The database to use for queries
     * @param Factories\ILoginCredentialsFactory $loginCredentialsFactory The factory to use when creating login credentials
     */
    public function __construct(SQL\Database $sqlDatabase, Factories\ILoginCredentialsFactory $loginCredentialsFactory)
    {
        parent::__construct($sqlDatabase);

        $this->loginCredentialsFactory = $loginCredentialsFactory;
    }

    /**
     * Adds credentials to the repo
     *
     * @param Credentials\ILoginCredentials $credentials The credentials to add to the repo
     * @param string $token The unhashed token
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\ILoginCredentials $credentials, $token = "")
    {
        // TODO: Implement
        return false;
    }

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param int $userID The ID of the user whose credentials these are
     * @param string $hashedToken The hashed token
     * @return bool True if successful, otherwise false
     */
    public function deauthorize($userID, $hashedToken)
    {
        // TODO: Implement
        return false;
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userID The ID of the user whose credentials we are searching for
     * @param string $hashedToken The hashed authentication token we are searching for
     * @return Credentials\ILoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIDAndToken($userID, $hashedToken)
    {
        // TODO: Implement
        return false;
    }
} 