<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Creates user objects
 */
namespace Opulence\Users\Factories;

use DateTime;
use Opulence\Users\User;

class UserFactory
{
    /**
     * @param int $id The database Id of this user
     * @param DateTime $dateCreated The date this user was created
     * @param array $roles The list of roles this user has
     * @return User A user object
     */
    public function createUser($id, DateTime $dateCreated, array $roles)
    {
        return new User($id, $dateCreated, $roles);
    }
} 