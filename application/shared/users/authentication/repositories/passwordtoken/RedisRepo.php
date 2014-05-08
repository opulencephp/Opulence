<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Redis repo for password tokens
 */
namespace RamODev\Application\Shared\Users\Authentication\Repositories\PasswordToken;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Repositories;

class RedisRepo extends Repositories\RedisRepo implements IPasswordTokenRepo
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
     * Adds a password token to the repo
     *
     * @param int $userId The Id of the user whose password we're adding
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add($userId, Cryptography\Token &$passwordToken, $hashedPassword)
    {
        $this->redisDatabase->getPHPRedis()->set("users:" . $userId . ":password", $passwordToken->getId());

        return true;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redisDatabase->deleteKeyPatterns("users:*:password") !== false;
    }

    /**
     * Gets the password token for a user
     *
     * @param int $userId The Id of the user whose password token we want
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     */
    public function getByUserId($userId)
    {
        $passwordTokenId = $this->redisDatabase->getPHPRedis()->get("users:" . $userId . ":password");

        if($passwordTokenId === false)
        {
            return false;
        }

        return $this->tokenRepo->getById($passwordTokenId);
    }

    /**
     * Gets the password token for a user that matches the input unhashed password
     *
     * @param int $userId The Id of the user whose password token we want
     * @param string $unhashedPassword The unhashed password
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndUnhashedPassword($userId, $unhashedPassword)
    {
        $passwordToken = $this->getByUserId($userId);

        if($passwordToken === false)
        {
            return false;
        }

        if($this->tokenRepo->getByIdAndUnhashedValue($passwordToken->getId(), $unhashedPassword) === false)
        {
            return false;
        }

        return $passwordToken;
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        return $this->tokenRepo->getHashedValue($id);
    }
} 