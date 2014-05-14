<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RamODev\Application\Shared\Models\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Models\Cryptography;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RamODev\Application\Shared\Models\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Models\Databases\SQL;
use RamODev\Application\Shared\Models\Repositories;
use RamODev\Application\TBA\Models\Configs;

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
     * Deactivates a token from use
     *
     * @param Cryptography\Token $token The token to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Cryptography\Token &$token)
    {
        $token->deactivate();

        return $this->write(__FUNCTION__, array(&$token));
    }

    /**
     * Deactivates all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're deactivating
     * @param int $userId The Id of the user whose tokens we're deactivating
     * @return bool True if successful, otherwise false
     */
    public function deactivateAllByUserId($typeId, $userId)
    {
        return $this->write(__FUNCTION__, array($typeId, $userId));
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
     * Gets all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return array|bool The list of tokens if successful, otherwise false
     */
    public function getAllByUserId($typeId, $userId)
    {
        return $this->read(__FUNCTION__, array($typeId, $userId));
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
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $id The Id of the token we're searching for
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByIdAndUserIdAndUnhashedValue($id, $typeId, $userId, $unhashedValue)
    {
        return $this->read(__FUNCTION__, array($id, $typeId, $userId, $this->getPepperedUnhashedValue($unhashedValue)));
    }

    /**
     * Gets a token for a user, which we can do if there's only a single token of this type per user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return Cryptography\Token|bool The list of tokens if successful, otherwise false
     */
    public function getByUserId($typeId, $userId)
    {
        return $this->read(__FUNCTION__, array($typeId, $userId));
    }

    /**
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndUnhashedValue($typeId, $userId, $unhashedValue)
    {
        return $this->read(__FUNCTION__, array($typeId, $userId, $this->getPepperedUnhashedValue($unhashedValue)));
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        return $this->read(__FUNCTION__, array($id), false);
    }

    /**
     * Gets the hash of a token, which is suitable for storage
     *
     * @param string $unhashedValue The unhashed token to hash
     * @param int $hashAlgorithm The hash algorithm constant to use in password_hash
     * @param int $cost The cost of the hash to use
     * @return string The hashed token
     */
    public function hashToken($unhashedValue, $hashAlgorithm, $cost)
    {
        return password_hash($this->getPepperedUnhashedValue($unhashedValue), $hashAlgorithm, array("cost" => $cost));
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
     * @param Cryptography\Token $token The data to write to the Redis repository
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

    /**
     * Appends a pepper to an unhashed token value
     *
     * @param string $unhashedValue The unhashed value to pepper
     * @return string The peppered unhashed value
     */
    private function getPepperedUnhashedValue($unhashedValue)
    {
        return $unhashedValue . Configs\AuthenticationConfig::TOKEN_PEPPER;
    }
} 