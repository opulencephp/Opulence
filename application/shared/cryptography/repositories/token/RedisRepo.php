<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Redis repo for tokens
 */
namespace RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Repositories;

class RedisRepo extends Repositories\RedisRepo implements ITokenRepo
{
    /**
     * Adds a token to the repo
     *
     * @param Cryptography\Token $token The token we're adding
     * @param string $hashedValue The hashed token value
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token &$token, $hashedValue)
    {
        $this->redisDatabase->getPHPRedis()->hMset("tokens:" . $token->getId(), array(
            "id" => $token->getId(),
            "hashedvalue" => $hashedValue,
            "validfrom" => $token->getValidFrom()->getTimestamp(),
            "validto" => $token->getValidTo()->getTimestamp()
        ));

        // Add this to the list of tokens
        $this->redisDatabase->getPHPRedis()->zAdd("tokens", $token->getValidTo()->getTimestamp(), $token->getId());

        // Wipe out any expired credentials, but first get a list of all the tokens
        $expiredTokenIds = $this->redisDatabase->getPHPRedis()->zRangeByScore("tokens", "-inf", time());
        $this->redisDatabase->getPHPRedis()->zRemRangeByScore("tokens", "-inf", time());

        foreach($expiredTokenIds as $expiredTokenId)
        {
            $this->redisDatabase->getPHPRedis()->del("tokens:" . $expiredTokenId);
        }

        return true;
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
        // As an added layer of security, we verify that the user is trying to deauthorize a valid token
        $hashedValue = $this->getHashedValue($token->getId());

        if($hashedValue === false || !password_verify($unhashedValue, $hashedValue))
        {
            return false;
        }

        return $this->redisDatabase->getPHPRedis()->hSet("tokens:" . $token->getId(), "validto", 0);
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redisDatabase->getPHPRedis()->del("tokens") !== false
        && $this->redisDatabase->deleteKeyPatterns("tokens:*");
    }

    /**
     * Gets a list of all the tokens
     *
     * @return array|bool The list of all the tokens if successful, otherwise false
     */
    public function getAll()
    {
        return $this->read("tokens", "createTokenFromId", false);
    }

    /**
     * Gets the token with the input Id
     *
     * @param int $id The Id of the token we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getById($id)
    {
        return $this->createTokenFromId($id);
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
        $tokenFromId = $this->createTokenFromId($id);

        if($tokenFromId === false)
        {
            return false;
        }

        $hashedValue = $this->getHashedValue($tokenFromId->getId());

        if($hashedValue === false || !password_verify($unhashedValue, $hashedValue))
        {
            return false;
        }

        return $tokenFromId;
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        return $this->redisDatabase->getPHPRedis()->hGet("tokens:" . $id, "hashedvalue");
    }

    /**
     * Creates a token object from cache using an Id
     *
     * @param int $tokenId The Id of the token we're searching for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    protected function createTokenFromId($tokenId)
    {
        // Cast to int just in case it is still in string-form, which is how Redis stores most data
        $tokenId = (int)$tokenId;
        $tokenHash = $this->redisDatabase->getPHPRedis()->hGetAll("tokens:" . $tokenId);

        if($tokenHash === array())
        {
            return false;
        }

        return new Cryptography\Token(
            (int)$tokenHash["id"],
            \DateTime::createFromFormat("U", $tokenHash["validfrom"], new \DateTimeZone("UTC")),
            \DateTime::createFromFormat("U", $tokenHash["validto"], new \DateTimeZone("UTC"))
        );
    }
} 