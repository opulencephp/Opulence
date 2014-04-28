<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving login credentials from a Redis database
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials\Repositories\LoginCredentials;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Factories;

class RedisRepo extends Repositories\RedisRepo implements ILoginCredentialsRepo
{
    /** @var Factories\ILoginCredentialsFactory The factory to use when creating login credentials */
    private $loginCredentialsFactory = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     * @param Factories\ILoginCredentialsFactory $loginCredentialsFactory The factory to use when creating login credentials
     */
    public function __construct(Redis\Database $redisDatabase, Factories\ILoginCredentialsFactory $loginCredentialsFactory)
    {
        parent::__construct($redisDatabase);

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
        // Store the credentials as a hash
        $this->redisDatabase->getPHPRedis()->hMset("users:" . $credentials->getUserId() . ":authentication:logincredentials:" . $credentials->getHashedToken(),
            array(
                "userid" => $credentials->getUserId(),
                "hashedtoken" => $credentials->getHashedToken(),
                "expiration" => $credentials->getExpiration()->getTimestamp()
            ));

        // Add these credentials to a list for this user
        $this->redisDatabase->getPHPRedis()->zAdd(
            "users:" . $credentials->getUserId() . ":authentication:logincredentials",
            $credentials->getExpiration()->getTimestamp(),
            $credentials->getHashedToken()
        );

        // Wipe out any expired credentials, but first get a list of all the tokens
        $expiredTokens = $this->redisDatabase->getPHPRedis()->zRangeByScore("users:" . $credentials->getUserId() . ":authentication:logincredentials", "-inf", time());
        $this->redisDatabase->getPHPRedis()->zRemRangeByScore("users:" . $credentials->getUserId() . ":authentication:logincredentials", "-inf", time());

        foreach($expiredTokens as $expiredToken)
        {
            $this->deauthorize($credentials->getUserId(), $expiredToken);
        }

        return true;
    }

    /**
     * Deauthorizes the input credentials from the repo
     *
     * @param int $userId The Id of the user whose credentials these are
     * @param string $hashedToken The hashed token
     * @return bool True if successful, otherwise false
     */
    public function deauthorize($userId, $hashedToken)
    {
        $this->redisDatabase->getPHPRedis()->del("users:" . $userId . ":authentication:logincredentials:" . $hashedToken);

        return $this->redisDatabase->getPHPRedis()->zRem("users:" . $userId . ":authentication:logincredentials", $hashedToken) !== 0;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redisDatabase->deleteKeyPatterns(array(
            "users:*:authentication:logincredentials",
            "users:*:authentication:logincredentials:*"
        ));
    }

    /**
     * Gets the login credentials that match the parameters
     *
     * @param int $userId The Id of the user whose credentials we are searching for
     * @param string $hashedToken The hashed authentication token we are searching for
     * @return Credentials\ILoginCredentials|bool The login credentials if successful, otherwise false
     */
    public function getByUserIdAndToken($userId, $hashedToken)
    {
        $credentialsHash = $this->redisDatabase->getPHPRedis()->hGetAll("users:" . $userId . ":authentication:logincredentials:" . $hashedToken);

        if($credentialsHash === array())
        {
            return false;
        }

        $credentials = $this->loginCredentialsFactory->createLoginCredentials(
            (int)$credentialsHash["userid"],
            \DateTime::createFromFormat("U", $credentialsHash["expiration"], new \DateTimeZone("UTC"))
        );
        $credentials->setHashedToken($hashedToken);

        // Make sure this hasn't expired
        if($credentials->getExpiration()->getTimestamp() < time())
        {
            $this->deauthorize($userId, $hashedToken);

            return false;
        }

        return $credentials;
    }
} 