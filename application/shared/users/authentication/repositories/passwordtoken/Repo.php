<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the repo for password tokens
 */
namespace RamODev\Application\Shared\Users\Authentication\Repositories\PasswordToken;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\TBA\Configs;

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements IPasswordTokenRepo
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
     * Adds a password token to the repo
     *
     * @param int $userId The Id of the user whose password we're adding
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add($userId, Cryptography\Token &$passwordToken, $hashedPassword)
    {
        if($this->tokenRepo->add($passwordToken, $hashedPassword) === false)
        {
            return false;
        }

        return $this->write(__FUNCTION__, array($userId, &$passwordToken, $hashedPassword));
    }

    /**
     * Gets the password token for a user
     *
     * @param int $userId The Id of the user whose password token we want
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     */
    public function getByUserId($userId)
    {
        return $this->read(__FUNCTION__, array($userId), true, array($userId));
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
        return $this->read(__FUNCTION__, array($userId, $unhashedPassword), true, array($userId));
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

    /**
     * Gets the hash of a password, which is suitable for storage
     *
     * @param string $unhashedPassword The unhashed password to hash
     * @return string The hashed password
     */
    public function hashPassword($unhashedPassword)
    {
        return $this->tokenRepo->hashToken($unhashedPassword, PASSWORD_BCRYPT,
            Configs\AuthenticationConfig::USER_PASSWORD_HASH_COST);
    }

    /**
     * Synchronizes the Redis repository with the SQL repository
     *
     * @return bool True if successful, otherwise false
     */
    public function sync()
    {
        // Don't bother reloading Redis because we'll just let that happen as users log back in
        return $this->redisRepo->flush();
    }

    /**
     * Updates a password token for a user in the repo
     *
     * @param int $userId The Id of the user whose password we're updating
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function update($userId, Cryptography\Token &$passwordToken, $hashedPassword)
    {
        return $this->write(__FUNCTION__, array($userId, &$passwordToken, $hashedPassword));
    }

    /**
     * In the case we're getting data and didn't find it in the Redis repo, we need a way to store it there for future use
     * The contents of this method should call the appropriate method to store data in the Redis repo
     *
     * @param Cryptography\Token $passwordToken The data to write to the Redis repository
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$passwordToken, $funcArgs = array())
    {
        $hashedPassword = $this->tokenRepo->getHashedValue($passwordToken->getId());

        if($hashedPassword !== false)
        {
            // The first argument will be the user Id
            $this->redisRepo->add($funcArgs[0], $passwordToken, $hashedPassword);
        }
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\Database $sqlDatabase)
    {
        return new PostgreSQLRepo($sqlDatabase, $this->tokenRepo);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Database $redisDatabase)
    {
        return new RedisRepo($redisDatabase, $this->tokenRepo);
    }
} 