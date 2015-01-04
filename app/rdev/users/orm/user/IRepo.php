<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the user repository interface
 */
namespace RDev\Users\ORM\User;
use RDev\ORM\Repositories;
use RDev\Users;

/**
 * @method Users\User getById($id)
 * @method Users\User[] getAll()
 */
interface IRepo extends Repositories\IRepo
{
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
} 