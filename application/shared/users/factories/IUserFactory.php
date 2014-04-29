<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the user factory interface
 */
namespace RamODev\Application\Shared\Users\Factories;
use RamODev\Application\Shared\Users;

interface IUserFactory
{
    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param string $hashedPassword The hashed password of this user
     * @param string $email The email address of this user
     * @param \DateTime $dateCreated The date this user was created
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     * @return Users\IUser A user object
     */
    public function createUser($id, $username, $hashedPassword, $email, $dateCreated, $firstName, $lastName);
} 