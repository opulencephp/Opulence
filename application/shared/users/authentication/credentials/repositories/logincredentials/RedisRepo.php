<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from a Redis database
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
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
     * @param Credentials\LoginCredentials $credentials The credentials to add to the repo
     * @param string $hashedLoginTokenValue The hashed login token value
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\LoginCredentials $credentials, $hashedLoginTokenValue)
    {
        $this->redisDatabase->getPHPRedis()->sAdd("users:" . $credentials->getUserId() . ":authentication:login",
            $credentials->getLoginToken()->getId());

        return true;
    }

    /**
     * Deactivates the input credentials from the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Credentials\LoginCredentials $credentials)
    {
        return $this->redisDatabase->getPHPRedis()->sRem("users:" . $credentials->getUserId() . ":authentication:login",
            $credentials->getLoginToken()->getId()) !== false;
    }

    /**
     * Deactivates all the login credentials for a user
     * This is useful in such cases like password changes where we want to deactivate all old sessions
     *
     * @param int $userId The Id of the user whose credentials we are deactivating
     * @return bool True if successful, otherwise false
     */
    public function deactivateAllByUserId($userId)
    {
        return $this->redisDatabase->getPHPRedis()->del("users:" . $userId . ":authentication:login");
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
     * Gets a list of all the login credentials for a user
     *
     * @param int $userId The Id of the user whose login credentials we want
     * @return array|bool The list of login credentials if successful, otherwise false
     */
    public function getAllByUserId($userId)
    {
        $loginCredentials = array();
        $tokenIds = $this->redisDatabase->getPHPRedis()->sMembers("users:" . $userId . ":authentication:login");

        if($tokenIds === false)
        {
            return false;
        }

        $tokenIds = array_map("intval", $tokenIds);

        foreach($tokenIds as $tokenId)
        {
            $token = $this->tokenRepo->getById($tokenId);

            if($token === false)
            {
                return false;
            }

            $loginCredentials[] = new Credentials\LoginCredentials($userId, $token);
        }

        return $loginCredentials;
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we're searching for
     * @param string $unhashedLoginTokenValue The unhashed login token we are searching for
     * @return Credentials\LoginCredentials|bool The login credentials if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
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