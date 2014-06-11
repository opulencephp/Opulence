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
     * @param \DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     * @return Users\User A user object
     */
    public function createUser($id, $username, \DateTime $dateCreated, array $roles)
    {
        return new Users\User($id, $username, $dateCreated, $roles);
    }
} 