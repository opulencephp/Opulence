<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from the repository
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements ILoginCredentialsRepo
{
    /** @var Factories\ILoginCredentialsFactory The factory to use to create login credentials */
    private $loginCredentialsFactory = null;
    /** @var string The pepper to use before hashing a token */
    private $tokenPepper = "";
    /** @var int The cost of the hash algorithm used to store tokens */
    private $hashCost = 11;

    /**
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     * @param Factories\ILoginCredentialsFactory $loginCredentialsFactory The factory to use to create login credentials
     * @param string @passwordPepper The pepper to use before hashing a token
     * @param int $hashCost The cost of the hash algorithm used to store tokens
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase, Factories\ILoginCredentialsFactory $loginCredentialsFactory, $tokenPepper, $hashCost)
    {
        $this->loginCredentialsFactory = $loginCredentialsFactory;
        $this->tokenPepper = $tokenPepper;
        $this->hashCost = $hashCost;

        parent::__construct($redisDatabase, $sqlDatabase);
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
        if(!empty($token))
        {
            $credentials->setHashedToken($this->getHashedToken($token));
        }

        return $this->write(__FUNCTION__, array($credentials));
    }

    /**
     * Deletes the input credentials from the repo
     *
     * @param int $userID The ID of the user whose credentials these are
     * @param string $hashedToken The hashed token
     * @return bool True if successful, otherwise false
     */
    public function deauthorize($userID, $hashedToken)
    {
        $this->write(__FUNCTION__, array($userID, $hashedToken));
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userID The ID of the user whose credentials we are searching for
     * @param string $unhashedToken The unhashed authentication token we are searching for
     * @return Credentials\LoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIDAndToken($userID, $unhashedToken)
    {
        return $this->read(__FUNCTION__, array($userID, $this->getHashedToken($unhashedToken)));
    }

    /**
     * Synchronizes the Redis repository with the SQL repository
     *
     * @return bool True if successful, otherwise false
     */
    public function sync()
    {
        // Don't bother reloading Redis, we'll let that happen when each user tries to login with credentials
        return $this->redisRepo->flush();
    }

    /**
     * In the case we're getting data and didn't find it in the Redis repo, we need a way to store it there for future use
     * The contents of this method should call the appropriate method to store data in the Redis repo
     *
     * @param mixed $data The data to write to the Redis repository
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$data, $funcArgs = array())
    {
        $this->redisRepo->add($data);
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return Repositories\PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\Database $sqlDatabase)
    {
        return new PostgreSQLRepo($sqlDatabase, $this->loginCredentialsFactory);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return Repositories\RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Database $redisDatabase)
    {
        return new RedisRepo($redisDatabase, $this->loginCredentialsFactory);
    }

    /**
     * Gets the hash of a token, which is suitable for storage
     *
     * @param string $token The unhashed token to hash
     * @return string The hashed token
     */
    private function getHashedToken($token)
    {
        return password_hash($token . $this->tokenPepper, PASSWORD_BCRYPT, array("cost" => $this->hashCost));
    }
} 