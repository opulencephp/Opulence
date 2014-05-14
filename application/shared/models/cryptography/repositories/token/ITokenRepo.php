<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for token repos to implement
 */
namespace RamODev\Application\Shared\Models\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Models\Cryptography;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;

interface ITokenRepo
{
    /**
     * Adds a token to the repo
     *
     * @param Cryptography\Token $token The token we're adding
     * @param string $hashedValue The hashed token value
     * @return bool True if successful, otherwise false
     */
    public function add(Cryptography\Token &$token, $hashedValue);

    /**
     * Deactivates a token from use
     *
     * @param Cryptography\Token $token The token to deactivate
     * @return bool True if successful, otherwise false
     */
    public function deactivate(Cryptography\Token &$token);

    /**
     * Deactivates all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're deactivating
     * @param int $userId The Id of the user whose tokens we're deactivating
     * @return bool True if successful, otherwise false
     */
    public function deactivateAllByUserId($typeId, $userId);

    /**
     * Gets a list of all the tokens
     *
     * @return array|bool The list of all the tokens if successful, otherwise false
     */
    public function getAll();

    /**
     * Gets all tokens for a user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return array|bool The list of tokens if successful, otherwise false
     */
    public function getAllByUserId($typeId, $userId);

    /**
     * Gets the token with the input Id
     *
     * @param int $id The Id of the token we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getById($id);

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
    public function getByIdAndUserIdAndUnhashedValue($id, $typeId, $userId, $unhashedValue);

    /**
     * Gets a token for a user, which we can do if there's only a single token of this type per user
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose tokens we're searching for
     * @return Cryptography\Token|bool The list of tokens if successful, otherwise false
     */
    public function getByUserId($typeId, $userId);

    /**
     * Gets the token for a user that matches the unhashed value
     *
     * @param int $typeId The Id of the type of token we're searching for
     * @param int $userId The Id of the user whose token we're searching for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     * @throws IncorrectHashException Thrown if the unhashed value doesn't match the hashed value
     */
    public function getByUserIdAndUnhashedValue($typeId, $userId, $unhashedValue);

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id);
} 