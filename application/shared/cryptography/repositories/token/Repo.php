<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Repositories;
use RamODev\Application\TBA\Configs;

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements ITokenRepo
{
    /** @var string The IP address of the user that is calling into this repo */
    private $ipAddress = "";
    /** @var string The user agent of the user that is calling into this repo */
    private $userAgent = "";

    /**
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     * @param string $ipAddress The IP address of the user that is calling into this repo
     * @param string $userAgent The user agent of the user that is calling into this repo
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase, $ipAddress, $userAgent)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;

        parent::__construct($redisDatabase, $sqlDatabase);
    }

    /**
     * Adds a token to the repo
     *
     * @param Cryptography\Token $token The token we're adding
     * @param string $hashedValue The hashed token value
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token &$token, $hashedValue)
    {
        return $this->write(__FUNCTION__, array(&$token, $hashedValue));
    }

    /**
     * Deauthorizes a token from use
     *
     * @param Cryptography\Token $token The token to deauthorize
     * @param string $unhashedValue The unhashed value of the token, which is used to verify we're deauthorizing the
     *      correct token
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Cryptography\Token $token, $unhashedValue)
    {
        return $this->write(__FUNCTION__, array($token, $unhashedValue . Configs\AuthenticationConfig::TOKEN_PEPPER));
    }

    /**
     * Gets a list of all the tokens
     *
     * @return array|bool The list of all the tokens if successful, otherwise false
     */
    public function getAll()
    {
        return $this->read(__FUNCTION__);
    }

    /**
     * Gets the token with the input Id
     *
     * @param int $id The Id of the token we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getById($id)
    {
        return $this->read(__FUNCTION__, array($id));
    }

    /**
     * Gets a token by its Id and unhashed value
     *
     * @param int $id The Id of the token we're looking for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getByIdAndUnhashedValue($id, $unhashedValue)
    {
        return $this->read(__FUNCTION__, array($id, $unhashedValue . Configs\AuthenticationConfig::TOKEN_PEPPER));
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        return $this->read(__FUNCTION__, array($id));
    }

    /**
     * Gets the hash of a token, which is suitable for storage
     *
     * @param string $token The unhashed token to hash
     * @return string The hashed token
     */
    public function hashToken($token)
    {
        return password_hash($token . Configs\AuthenticationConfig::TOKEN_PEPPER, PASSWORD_BCRYPT,
            array("cost" => Configs\AuthenticationConfig::TOKEN_HASH_COST));
    }

    /**
     * Synchronizes the Redis repository with the SQL repository
     *
     * @return bool True if successful, otherwise false
     */
    public function sync()
    {
        return $this->redisRepo->flush() && $this->getAll() !== false;
    }

    /**
     * In the case we're getting data and didn't find it in the Redis repo, we need a way to store it there for future use
     * The contents of this method should call the appropriate method to store data in the Redis repo
     *
     * @param mixed $token The data to write to the Redis repository
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$token, $funcArgs = array())
    {
        $this->redisRepo->add($token, $this->getHashedValue($token->getId()));
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\Database $sqlDatabase)
    {
        return new PostgreSQLRepo($sqlDatabase, $this->ipAddress, $this->userAgent);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Database $redisDatabase)
    {
        return new RedisRepo($redisDatabase);
    }
} 