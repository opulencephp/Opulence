<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from a Redis database
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;

class RedisRepo extends Repositories\RedisRepo implements ILoginCredentialsRepo
{
    /** @var Token\ITokenRepo The token repo */
    private $tokenRepo = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     * @param Token\ITokenRepo $tokenRepo The token repo
     */
    public function __construct(Redis\Database $redisDatabase, Token\ITokenRepo $tokenRepo)
    {
        parent::__construct($redisDatabase);

        $this->tokenRepo = $tokenRepo;
    }

    /**
     * Adds credentials to the repo
     *
     * @param Credentials\ILoginCredentials $credentials The credentials to add to the repo
     * @param string $hashedLoginTokenValue The hashed login token value
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\ILoginCredentials $credentials, $hashedLoginTokenValue)
    {
        $this->redisDatabase->getPHPRedis()->sAdd("users:" . $credentials->getUserId() . ":authentication:login",
            $credentials->getLoginToken()->getId());

        return true;
    }

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param Credentials\ILoginCredentials $credentials The credentials to deauthorize
     * @param string $unhashedLoginTokenValue The unhashed token value
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Credentials\ILoginCredentials $credentials, $unhashedLoginTokenValue)
    {
        return $this->tokenRepo->deauthorize($credentials->getLoginToken(), $unhashedLoginTokenValue)
        && $this->redisDatabase->getPHPRedis()->sRem("users:" . $credentials->getUserId() . ":authentication:login",
            $credentials->getLoginToken()->getId()) !== false;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redisDatabase->deleteKeyPatterns(array("users:*:authentication:login"));
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we're searching for
     * @param string $unhashedLoginTokenValue The unhashed login token we are searching for
     * @return Credentials\ILoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndLoginToken($userId, $loginTokenId, $unhashedLoginTokenValue)
    {
        $loginToken = $this->tokenRepo->getByIdAndUnhashedValue($loginTokenId, $unhashedLoginTokenValue);

        if($loginToken === false)
        {
            return false;
        }

        if(!$this->redisDatabase->getPHPRedis()->sIsMember("users:" . $userId . ":authentication:login", $loginTokenId))
        {
            return false;
        }

        return new Credentials\LoginCredentials($userId, $loginToken);
    }
} 