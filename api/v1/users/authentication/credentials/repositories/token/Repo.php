<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the authentication token repository
 */
namespace RamODev\API\V1\Users\Authentication\Credentials\Repositories\Token;
use RamODev\API\V1\Cryptography;
use RamODev\Databases\NoSQL\Redis;
use RamODev\Databases\SQL;
use RamODev\Repositories;

require_once(__DIR__ . "/../../../../../../../repositories/RedisWithPostgreSQLBackupRepo.php");
require_once(__DIR__ . "/../../../../../cryptography/Token.php");

class Repo extends Repositories\RedisWithPostgreSQLBackupRepo implements ITokenRepo
{
    /** @var Cryptography\Factories\TokenFactory The factory to generate tokens */
    private $tokenFactory = null;

    /**
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     * @param Cryptography\Factories\TokenFactory $tokenFactory The factory to generate tokens
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase, Cryptography\Factories\TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;

        parent::__construct($redisDatabase, $sqlDatabase);
    }

    /**
     * Adds a token to the repo
     *
     * @param int $userID The ID of the user whose token we're storing
     * @param Cryptography\Token $token The token to store
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token $token, $userID)
    {
        return $this->set(__FUNCTION__, array($userID, $token));
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
        return $this->set(__FUNCTION__, array($userID, $token));
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
        return $this->get(__FUNCTION__, array($userID, $tokenString, $expiration, $salt, $secretKey));
    }

    /**
     * Stores a user object that wasn't initially found in the Redis repo
     *
     * @param Cryptography\Token $token The token to store in the Redis repo
     * @param array $funcArgs The array of function arguments to pass into the method that adds the data to the Redis repo
     */
    protected function addDataToRedisRepo(&$token, $funcArgs = array())
    {
        $this->redisRepo->add($token, func_get_arg(1));
    }

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return PostgreSQLRepo The SQL repo to use
     */
    protected function getPostgreSQLRepo(SQL\Database $sqlDatabase)
    {
        return new PostgreSQLRepo($sqlDatabase, $this->tokenFactory);
    }

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return RedisRepo The Redis repo to use
     */
    protected function getRedisRepo(Redis\Database $redisDatabase)
    {
        return new RedisRepo($redisDatabase, $this->tokenFactory);
    }
} 