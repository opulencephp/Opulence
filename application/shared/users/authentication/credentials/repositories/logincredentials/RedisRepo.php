<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from a Redis database
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;

class RedisRepo extends Repositories\RedisRepo implements ILoginCredentialsRepo
{
    /**
     * Adds credentials to the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to add to the repo
     * @return bool True if successful, otherwise false
     */
    public function add(Credentials\LoginCredentials $credentials)
    {
        // Store the credentials as a hash
        $this->redisDatabase->getPHPRedis()->hMset(
            "users:" . $credentials->getUserId() . ":authentication:login:" . $credentials->getLoginToken()->getId(),
            array(
                "value" => $credentials->getLoginToken()->getValue(),
                "validfrom" => $credentials->getLoginToken()->getValidFrom()->getTimestamp(),
                "validto" => $credentials->getLoginToken()->getValidTo()->getTimestamp()
            ));

        // Add these credentials to a list for this user
        $this->redisDatabase->getPHPRedis()->zAdd(
            "users:" . $credentials->getUserId() . ":authentication:login",
            $credentials->getLoginToken()->getValidTo()->getTimestamp(),
            $credentials->getLoginToken()->getValue()
        );

        // Wipe out any expired credentials, but first get a list of all the tokens
        $expiredTokenIds = $this->redisDatabase->getPHPRedis()
            ->zRangeByScore("users:" . $credentials->getUserId() . ":authentication:login", "-inf", time());
        $this->redisDatabase->getPHPRedis()
            ->zRemRangeByScore("users:" . $credentials->getUserId() . ":authentication:login", "-inf", time());

        foreach($expiredTokenIds as $expiredTokenId)
        {
            if(!$this->deauthorize($this->createCredentialsFromLoginTokenId($credentials->getUserId(), $expiredTokenId)))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param Credentials\LoginCredentials $credentials The credentials to deauthorize
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Credentials\LoginCredentials $credentials)
    {
        $this->redisDatabase->getPHPRedis()->del(
            "users:" . $credentials->getUserId() . ":authentication:login:" . $credentials->getLoginToken()->getId());

        return $this->redisDatabase->getPHPRedis()->zRem("users:" . $credentials->getUserId() . ":authentication:login",
            $credentials->getLoginToken()->getId()) !== 0;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redisDatabase->deleteKeyPatterns(array(
            "users:*:authentication:login",
            "users:*:authentication:login:*"
        ));
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param int $loginTokenId The Id of the login token we are searching for
     * @param string $loginTokenValue The hashed authentication token we are searching for
     * @return Credentials\LoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndLoginToken($userId, $loginTokenId, $loginTokenValue)
    {
        $credentials = $this->createCredentialsFromLoginTokenId($userId, $loginTokenId);

        if(!$credentials || $credentials->getLoginToken()->getValue() !== $loginTokenValue)
        {
            return false;
        }

        // Make sure this hasn't expired
        if($credentials->getLoginToken()->getValidTo()->getTimestamp() < time())
        {
            $this->deauthorize($credentials);

            return false;
        }

        return $credentials;
    }

    /**
     * Creates credentials from a login token ID
     *
     * @param int $userId The Id of the user whose login token we're creating
     * @param int $loginTokenId The Id of the login token
     * @return Credentials\LoginCredentials|bool The credentials if successful, otherwise false
     */
    protected function createCredentialsFromLoginTokenId($userId, $loginTokenId)
    {
        $loginTokenHash = $this->redisDatabase->getPHPRedis()
            ->hGetAll("users:" . $userId . ":authentication:login:" . $loginTokenId);

        if($loginTokenHash === array())
        {
            return false;
        }

        $loginToken = new Cryptography\Token(
            $loginTokenId,
            $loginTokenHash["value"],
            \DateTime::createFromFormat("U", $loginTokenHash["validfrom"], new \DateTimeZone("UTC")),
            \DateTime::createFromFormat("U", $loginTokenHash["validto"], new \DateTimeZone("UTC"))
        );

        return new Credentials\LoginCredentials($userId, $loginToken);
    }
} 