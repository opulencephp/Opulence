<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates user objects
 */
namespace RamODev\API\V1\Users\Factories;
use RamODev\API\V1\Users;

require_once(__DIR__ . "/../User.php");
require_once(__DIR__ . "/IUserFactory.php");

class UserFactory implements IUserFactory
{
    /**
     * @param int $id The ID of this user
     * @param string $username The username of the user
     * @param string $hashedPassword The hashed password of this user
     * @param string $email The email address of this user
     * @param \DateTime $dateCreated The date this user was created
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     * @return Users\IUser A user object
     */
    public function createUser($id, $username, $hashedPassword, $email, $dateCreated, $firstName, $lastName)
    {
        return new Users\User($id, $username, $hashedPassword, $email, $dateCreated, $firstName, $lastName);
    }
} 