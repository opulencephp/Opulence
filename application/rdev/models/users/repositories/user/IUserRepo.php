<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user repository interface
 */
namespace RDev\Models\Users\Repositories\User;
use RDev\Models\Repositories\Exceptions as RepoExceptions;
use RDev\Models\Users;

interface IUserRepo
{
    /**
     * Adds a user to the repository
     *
     * @param Users\IUser $user The user to store in the repository
     * @throws RepoExceptions\RepoException Thrown if there was an error adding the user
     */
    public function add(Users\IUser &$user);

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
     * Saves a user's properties to the repo
     *
     * @param Users\IUser $user The user to save
     * @throws RepoExceptions\RepoException Thrown if there was an error saving the user
     */
    public function save(Users\IUser &$user);
} 