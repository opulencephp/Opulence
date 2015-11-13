<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Users\Factories;

use DateTime;
use Opulence\Users\User;

/**
 * Creates user objects
 */
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