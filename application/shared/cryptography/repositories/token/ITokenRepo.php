<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for token repos to implement
 */
namespace RamODev\Application\Shared\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Cryptography;
use RamODev\Application\Shared\Cryptography\Repositories\Token\Exceptions\IncorrectHashException;

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
     * Gets a list of all the tokens
     *
     * @return array|bool The list of all the tokens if successful, otherwise false
     */
    public function getAll();

    /**
     * Gets the token with the input Id
     *
     * @param int $id The Id of the token we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getById($id);

    /**
     * Gets a token by its Id and unhashed value
     *
     * @param int $id The Id of the token we're looking for
     * @param string $unhashedValue The unhashed value we're looking for
     * @return Cryptography\Token|bool The token if successful, otherwise false
     */
    public function getByIdAndUnhashedValue($id, $unhashedValue);

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id);
} 