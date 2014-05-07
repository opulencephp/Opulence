<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from the repository
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;
use RamODev\Application\TBA\Configs;

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements ILoginCredentialsRepo
{
    /** @var Token\ITokenRepo The token repo */
    private $tokenRepo = null;

    /**
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     * @param Token\ITokenRepo $tokenRepo The token repo
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase, Token\ITokenRepo $tokenRepo)
    {
        $this->tokenRepo = $tokenRepo;

        parent::__construct($redisDatabase, $sqlDatabase);
    }

    /**
     * Adds credentials to the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to add to the repo
     * @param string $hashedLoginTokenValue The hashed login token value
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\LoginCredentials $credentials, $hashedLoginTokenValue)
    {
        /**
         * When we add the login token to the repo, its ID will be set
         * So, we grab it from the credentials, add it to the repo, then re-add it to the credentials
         */
        $loginToken = $credentials->getLoginToken();

        if($this->tokenRepo->add($loginToken, $hashedLoginTokenValue) === false)
        {
            return false;
        }

        $credentials->setLoginToken($loginToken);

        return $this->write(__FUNCTION__, array($credentials, $hashedLoginTokenValue));
    }

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to deauthorize
     * @param string $unhashedLoginTokenValue The unhashed token value
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Credentials\LoginCredentials $credentials, $unhashedLoginTokenValue)
    {
        // We deauthorize the token here instead of in the child repos so that it doesn't get called twice
        return $this->tokenRepo->deauthorize($credentials->getLoginToken(), $unhashedLoginTokenValue)
        && $this->write(__FUNCTION__, array($credentials, $unhashedLoginTokenValue));
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we're searching for
     * @param string $unhashedLoginTokenValue The hashed login token we are searching for
     * @return Credentials\LoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndLoginToken($userId, $loginTokenId, $unhashedLoginTokenValue)
    {
        return $this->read(__FUNCTION__, array($userId, $loginTokenId, $unhashedLoginTokenValue));
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
     * @param Credentials\LoginCredentials $credentials The data to write to the Redis repository
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$credentials, $funcArgs = array())
    {
        $this->redisRepo->add($credentials, $this->tokenRepo->getHashedValue($credentials->getLoginToken()->getId()));
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return Repositories\PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\Database $sqlDatabase)
    {
        return new PostgreSQLRepo($sqlDatabase, $this->tokenRepo);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return Repositories\RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Database $redisDatabase)
    {
        return new RedisRepo($redisDatabase, $this->tokenRepo);
    }
} 