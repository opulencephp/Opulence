<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the token repository interface
 */
namespace RamODev\API\V1\Users\Authentication\Credentials\Repositories\Token;
use RamODev\API\V1\Cryptography;

interface ITokenRepo
{
    /**
     * Adds a token to the repo
     *
     * @param Cryptography\Token $token The token to store
     * @param int $userID The ID of the user whose token we're storing
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token $token, $userID);

    /**
     * Deauthorizes the input token for the input user
     *
     * @param Cryptography\Token $token The token to deauthorize
     * @param int $userID The ID of the user whose token we're deauthorizing
     * @return bool True if successful, otherwise false
     */
    public function deauthorize(Cryptography\Token $token, $userID);

    /**
     * Gets the token for the input user
     *
     * @param string $tokenString The token to match
     * @param \DateTime $expiration The expiration time to match
     * @param string $hmac The HMAC to match
     * @param int $userID The ID of the user whose token we want
     * @return Cryptography\Token|bool The token for the user if successful, otherwise false
     */
    public function getByTokenDataAndUserID($tokenString, $expiration, $hmac, $userID);
} 