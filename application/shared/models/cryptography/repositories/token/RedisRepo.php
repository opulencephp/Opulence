<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Redis repo for tokens
 */
namespace RDev\Application\Shared\Models\Cryptography\Repositories\Token;
use RDev\Application\Shared\Models\Cryptography;
use RDev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;
use RDev\Application\Shared\Models\Repositories;

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
        $this->redis->hMset("tokens:" . $token->getId(), array(
            "id" => $token->getId(),
            "tokentypeid" => $token->getTypeId(),
            "userid" => $token->getUserId(),
            "hashedvalue" => $hashedValue,
            "validfrom" => $token->getValidFrom()->getTimestamp(),
            "validto" => $token->getValidTo()->getTimestamp(),
            "isactive" => $token->isActive()
        ));

        // Add this to the list of tokens
        $this->redis->zAdd("tokens", $token->getValidTo()->getTimestamp(), $token->getId());
        // Add this to a user index
        $this->redis
            ->sAdd("tokens:types:" . $token->getTypeId() . ":users:" . $token->getUserId(), $token->getId());
        // Wipe out any expired credentials
        $this->redis->zRemRangeByScore("tokens", "-inf", time());

        return true;
    }

    /**
     * Deactivates a token from use
     *
     * @param Cryptography\Token $token The token to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Cryptography\Token &$token)
    {
        return $this->deactivateById($token->getId());
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
        $tokenIds = $this->redis->sMembers("tokens:types:" . $typeId . ":users:" . $userId);
        $tokenIds = array_map("intval", $tokenIds);

        foreach($tokenIds as $tokenId)
        {
            if($this->deactivateById($tokenId) === false)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    public function flush()
    {
        return $this->redis->del("tokens") !== false
        && $this->redis->deleteKeyPatterns("tokens:*");
    }

    /**
     * Gets a list of all the tokens
     *
     * @return array|bool The list of all the tokens if successful, otherwise false
     */
    public function getAll()
    {
        return $this->read("tokens", false);
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
        return $this->read("tokens:types:" . $typeId . ":users:" . $userId, false);
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
        /** @var Cryptography\Token $tokenFromId */
        $tokenFromId = $this->getById($id);

        if($tokenFromId === false || $tokenFromId->getUserId() !== $userId || $tokenFromId->getTypeId() !== $typeId)
        {
            return false;
        }

        $hashedValue = $this->getHashedValue($tokenFromId->getId());

        if($hashedValue === false)
        {
            return false;
        }

        if(!password_verify($unhashedValue, $hashedValue))
        {
            throw new IncorrectHashException("Incorrect hash");
        }

        return $tokenFromId;
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
        return $this->read("tokens:types:" . $typeId . ":users:" . $userId, true);
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
        $tokenFromUserId = $this->read("tokens:types:" . $typeId . ":users:" . $userId, true);

        if($tokenFromUserId === false)
        {
            return false;
        }

        $hashedValue = $this->getHashedValue($tokenFromUserId->getId());

        if($hashedValue === false)
        {
            return false;
        }

        if(!password_verify($unhashedValue, $hashedValue))
        {
            throw new IncorrectHashException("Incorrect hash");
        }

        return $tokenFromUserId;
    }

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id)
    {
        return $this->redis->hGet("tokens:" . $id, "hashedvalue");
    }

    /**
     * Gets the hash representation of an entity
     *
     * @param int $id The Id of the entity whose hash we're searching for
     * @return array|bool The entity's hash if successful, otherwise false
     */
    protected function getEntityHashById($id)
    {
        return $this->redis->hGetAll("tokens:" . $id);
    }

    /**
     * Loads an entity from a hash of data
     *
     * @param array $hash The hash of data
     * @return Cryptography\Token The entity
     */
    protected function loadEntity(array $hash)
    {
        return new Cryptography\Token(
            (int)$hash["id"],
            (int)$hash["tokentypeid"],
            (int)$hash["userid"],
            \DateTime::createFromFormat("U", $hash["validfrom"], new \DateTimeZone("UTC")),
            \DateTime::createFromFormat("U", $hash["validto"], new \DateTimeZone("UTC")),
            $hash["isactive"]
        );
    }

    /**
     * Deactivates the token with the input Id
     *
     * @param int $id The Id of the token to deactivate
     * @return bool True if successful, otherwise false
     */
    private function deactivateById($id)
    {
        return $this->redis->hSet("tokens:" . $id, "isactive", false) !== false;
    }
} 