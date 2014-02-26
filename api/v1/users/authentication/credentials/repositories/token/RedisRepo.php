<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Redis repository for tokens
 */
namespace RamODev\API\V1\Users\Authentication\Credentials\Repositories\Token;
use RamODev\API\V1\Cryptography;
use RamODev\Databases\NoSQL\Redis;
use RamODev\Repositories;

class RedisRepo extends Repositories\RedisRepo implements ITokenRepo
{
    /** @var Cryptography\Factories\TokenFactory The factory to generate tokens */
    private $tokenFactory = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     * @param Cryptography\Factories\TokenFactory $tokenFactory The factory to generate tokens
     */
    public function __construct(Redis\Database $redisDatabase, Cryptography\Factories\TokenFactory $tokenFactory)
    {
        parent::__construct($redisDatabase);

        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Adds a token to the repo
     *
     * @param Cryptography\Token $token The token to store
     * @param int $userID The ID of the user whose token we're storing
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token $token, $userID)
    {
        // Add this token
        $this->redisDatabase->getPHPRedis()->hMset($this->getTokenHashKey($token->getTokenString(), $userID),
            array(
                "tokenString" => $token->getTokenString(),
                "expiration" => $token->getExpiration()->getTimestamp(),
                "salt" => $token->getSalt(),
                "secretKey" => $token->getSecretKey()
            )
        );
        // Create an index
        $this->redisDatabase->getPHPRedis()->zAdd($this->getTokenListKey($userID), $token->getExpiration()->getTimestamp(), $token->getTokenString());
        // Remove old tokens
        $this->redisDatabase->getPHPRedis()->zRemRangeByScore($this->getTokenListKey($userID), 0, time());

        return true;
    }

    /**
     * Deauthorizes the input token for the input user
     *
     * @param Cryptography\Token $token The token to deauthorize
     * @param int $userID The ID of the user whose token we're deauthorizing
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Cryptography\Token $token, $userID)
    {
        $this->redisDatabase->getPHPRedis()->del($this->getTokenHashKey($token->getTokenString(), $userID));
        $this->redisDatabase->getPHPRedis()->zRem($this->getTokenListKey($userID), $token->getTokenString());

        return true;
    }

    /**
     * Gets the token for the input user
     *
     * @param string $tokenString The token to match
     * @param \DateTime $expiration The expiration time to match
     * @param string $salt The unique salt to use in the HMAC
     * @param string $secretKey The secret key to use in the HMAC
     * @param int $userID The ID of the user whose token we want
     * @return Cryptography\Token|bool The token for the user if successful, otherwise false
     */
    public function getByTokenDataAndUserID($tokenString, $expiration, $salt, $secretKey, $userID)
    {
        $tokenHash = $this->redisDatabase->getPHPRedis()->hGetAll($this->getTokenHashKey($tokenString, $userID));

        if($tokenHash == array() || $tokenHash["tokenString"] != $tokenString || $tokenHash["expiration"] != $expiration->getTimestamp() || $tokenHash["salt"] != $salt || $tokenHash["secretKey"] != $secretKey)
        {
            return false;
        }

        return $this->tokenFactory->createToken($tokenHash["tokenString"], \DateTime::createFromFormat("U", $tokenHash["expiration"]), $tokenHash["salt"], $tokenHash["secretKey"]);
    }

    /**
     * Gets the key to use for the token hash
     *
     * @param string $tokenString The token string we're searching for
     * @param int $userID The ID of the user whose token we want
     * @return string Gets the key for the token hash
     */
    private function getTokenHashKey($tokenString, $userID)
    {
        return "users:$userID:authentication:tokens:$tokenString";
    }

    /**
     * Gets the key to use for searching the list of tokens
     *
     * @param int $userID The ID of the user whose token we want
     * @return string Gets the key for the list of tokens
     */
    private function getTokenListKey($userID)
    {
        return "users:$userID:authentication:tokens";
    }
} 