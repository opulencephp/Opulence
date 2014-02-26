<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the PostgreSQL repository for tokens
 */
namespace RamODev\API\V1\Users\Authentication\Credentials\Repositories\Token;
use RamODev\API\V1\Cryptography;
use RamODev\Databases\SQL;
use RamODev\Repositories;

class PostgreSQLRepo extends Repositories\PostgreSQLRepo implements ITokenRepo
{
    /** @var Cryptography\Factories\TokenFactory The factory to generate tokens */
    private $tokenFactory = null;

    /**
     * @param SQL\Database $sqlDatabase The database to use for queries
     * @param Cryptography\Factories\TokenFactory $tokenFactory The factory to generate tokens
     */
    public function __construct(SQL\Database $sqlDatabase, Cryptography\Factories\TokenFactory $tokenFactory)
    {
        parent::__construct($sqlDatabase);

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
        // Todo: Implement
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
        // Todo: Implement
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
        // Todo: Implement
    }
} 