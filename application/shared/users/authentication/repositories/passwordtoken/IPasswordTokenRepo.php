<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for password token repos to implement
 */
namespace RamODev\Application\Shared\Users\Authentication\Repositories\PasswordToken;
use RamODev\Application\Shared\Cryptography;

interface IPasswordTokenRepo
{
    /**
     * Adds a password token to the repo
     *
     * @param int $userId The Id of the user whose password we're adding
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function add($userId, Cryptography\Token &$passwordToken, $hashedPassword);

    /**
     * Gets the password token for a user
     *
     * @param int $userId The Id of the user whose password token we want
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     */
    public function getByUserId($userId);

    /**
     * Gets the password token for a user that matches the input unhashed password
     *
     * @param int $userId The Id of the user whose password token we want
     * @param string $unhashedPassword The unhashed password
     * @return Cryptography\Token|bool The password token if successful, otherwise false
     */
    public function getByUserIdAndUnhashedPassword($userId, $unhashedPassword);

    /**
     * Gets the hashed value for a token
     *
     * @param int $id The Id of the hash whose value we're searching for
     * @return string|bool The hashed value if successful, otherwise false
     */
    public function getHashedValue($id);

    /**
     * Updates a password token for a user in the repo
     *
     * @param int $userId The Id of the user whose password we're updating
     * @param Cryptography\Token $passwordToken The token containing data about the password
     * @param string $hashedPassword The hashed password
     * @return bool True if successful, otherwise false
     */
    public function update($userId, Cryptography\Token &$passwordToken, $hashedPassword);
} 