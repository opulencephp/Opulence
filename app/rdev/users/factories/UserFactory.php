<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Creates user objects
 */
namespace RDev\Users\Factories;
use RDev\Users;

class UserFactory
{
    /**
     * @param int $id The database Id of this user
     * @param \DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     * @return Users\User A user object
     */
    public function createUser($id, \DateTime $dateCreated, array $roles)
    {
        return new Users\User($id, $dateCreated, $roles);
    }
} 