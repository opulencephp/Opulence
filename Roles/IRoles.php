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
     * Assigns roles to a subject
     *
     * @param int|string $primaryIdentity The subject identity to assign to
     * @param string|string[] $roleNames The name of the role or roles to assign
     * @throws InvalidArgumentException Thrown if the input rule names do not exist
     */
    public function assignRoles($primaryIdentity, $roleNames);

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
     * Gets all roles for a subject
     *
     * @param int|string $primaryIdentity The subject identity whose roles we want
     * @return Role[] The list of roles
     */
    public function getRolesForSubject($primaryIdentity) : array;

    /**
     * Gets the list of subject identities with the role
     *
     * @param string $roleName The name of the role to search for
     * @return array The list of subject identities with the role
     */
    public function getSubjectIdentitiesWithRole(string $roleName) : array;

    /**
     * Removes all roles from a subject
     *
     * @param int|string $primaryIdentity The primary identity of the subject to remove
     */
    public function removeAllRolesFromSubject($primaryIdentity);

    /**
     * Removes roles from a subject
     *
     * @param int|string $primaryIdentity The identity of the subject to remove
     * @param string|string[] $roleNames The name of the role or roles to remove
     */
    public function removeRolesFromSubject($primaryIdentity, $roleNames);

    /**
     * Gets whether or not a role exists
     *
     * @param string $roleName The name of the role to search for
     * @return bool True if the role exists, otherwise false
     */
    public function roleExists(string $roleName) : bool;

    /**
     * Gets whether or not a subject has a role
     *
     * @param int|string $primaryIdentity The Id of the subject
     * @param string $roleName The name of the role to search for
     * @return bool True if the subject has the role, otherwise false
     */
    public function subjectHasRole($primaryIdentity, string $roleName) : bool;
}