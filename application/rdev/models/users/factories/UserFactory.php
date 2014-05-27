<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates user objects
 */
namespace RDev\Models\Users\Factories;
use RDev\Models\Users;

class UserFactory
{
    /**
     * @param int $id The database Id of this user
     * @param string $username The username of the user
     * @param string $email The email address of this user
     * @param \DateTime $dateCreated The date this user was created
     * @param string $firstName The first name of this user
     * @param string $lastName The last name of this user
     * @param array $roles The list of roles this user has
     * @return Users\User A user object
     */
    public function createUser($id, $username, $email, \DateTime $dateCreated, $firstName, $lastName, array $roles)
    {
        return new Users\User($id, $username, $email, $dateCreated, $firstName, $lastName, $roles);
    }
} 