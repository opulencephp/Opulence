<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the user repository interface
 */
namespace RDev\Users\ORM\User;
use RDev\ORM\Repositories\IRepo as IORMRepo;
use RDev\Users\IUser;
use RDev\Users\User;

/**
 * @method User getById($id)
 * @method User[] getAll()
 */
interface IRepo extends IORMRepo
{
    /**
     * Gets the user with the input username
     *
     * @param string $username The username to search for
     * @return IUser|bool The user with the input username if successful, otherwise false
     */
    public function getByUsername($username);

    /**
     * Gets the user with the input username and hashed password
     *
     * @param string $username The username to search for
     * @param string $unhashedPassword The unhashed password to search for
     * @return IUser|bool The user with the input username and password if successful, otherwise false
     */
    public function getByUsernameAndPassword($username, $unhashedPassword);
} 