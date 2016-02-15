<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Roles;

use InvalidArgumentException;

/**
 * Defines the interface for role managers to implement
 */
interface IRoles
{
    /**
     * Assigns roles to a user
     *
     * @param int|string $userId The user Id to assign to
     * @param string|string[] $roleNames The name of the role or roles to assign
     * @throws InvalidArgumentException Thrown if the input rule names do not exist
     */
    public function assignRoles($userId, $roleNames);

    /**
     * Creates a role
     *
     * @param string $roleName The name of the role
     * @return Role Creates a role
     */
    public function createRole(string $roleName) : Role;

    /**
     * Deletes a role
     *
     * @param string $roleName The name of the role to delete
     */
    public function deleteRole(string $roleName);

    /**
     * Gets all roles for a user
     *
     * @param int|string $userId The user Id whose roles we want
     * @return Role[] The list of roles
     */
    public function getRolesForUser($userId) : array;

    /**
     * Gets the list of user Ids with the role
     *
     * @param string $roleName The name of the role to search for
     * @return array The list of user Ids with the role
     */
    public function getUserIdsWithRole(string $roleName) : array;

    /**
     * Removes all roles from a user
     *
     * @param int|string $userId The Id of the user to remove
     */
    public function removeAllRolesFromUser($userId);

    /**
     * Removes roles from a user
     *
     * @param int|string $userId The Id of the user to remove
     * @param string|string[] $roleNames The name of the role or roles to remove
     */
    public function removeRolesFromUser($userId, $roleNames);

    /**
     * Gets whether or not a role exists
     *
     * @param string $roleName The name of the role to search for
     * @return bool True if the role exists, otherwise false
     */
    public function roleExists(string $roleName) : bool;

    /**
     * Gets whether or not a user has a role
     *
     * @param int|string $userId The Id of the user
     * @param string $roleName The name of the role to search for
     * @return bool True if the user has the role, otherwise false
     */
    public function userHasRole($userId, string $roleName) : bool;
}