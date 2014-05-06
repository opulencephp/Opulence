<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user repository interface
 */
namespace RamODev\Application\Shared\Users\Repositories\User;
use RamODev\Application\Shared\Users;

interface IUserRepo
{
    /**
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @param string $password The user's password
     * @return bool True if successful, otherwise false
     */
    public function add(Users\IUser &$user, $password);

    /**
     * Gets all the users in the repository
     *
     * @return array|bool The array of users if successful, otherwise false
     */
    public function getAll();

    /**
     * Gets the user with the input email
     *
     * @param string $email The email we're searching for
     * @return Users\IUser|bool The user that has the input email if successful, otherwise false
     */
    public function getByEmail($email);

    /**
     * Gets the user with the input Id
     *
     * @param int $id The database Id of the user we're searching for
     * @return Users\IUser|bool The user with the input Id if successful, otherwise false
     */
    public function getById($id);

    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return Users\IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username);

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $unhashedPassword The unhashed password to search for
     * @return Users\IUser|bool The user with the input username and password if successful, otherwise false
     */
    public function getByUsernameAndPassword($username, $unhashedPassword);

    /**
     * Gets a user's hashed password from the repo
     *
     * @param int $userId The Id of the user whose password we are searching for
     * @return string|bool The hashed password if successful, otherwise false
     */
    public function getHashedPassword($userId);

    /**
     * Updates a user's email address in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @param string $email The new email address
     * @return bool True if successful, otherwise false
     */
    public function updateEmail(Users\IUser &$user, $email);

    /**
     * Updates a user's password in the repository
     *
     * @param Users\IUser $user The user to update in the repository
     * @param string $password The unhashed new password
     * @return bool True if successful, otherwise false
     */
    public function updatePassword(Users\IUser &$user, $password);
} 