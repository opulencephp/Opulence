<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\Roles;

/**
 * Defines the interface for role managers to implement
 */
interface IRoles
{
    /**
     * Assigns roles to a subject
     *
     * @param int|string $subjectId The subject identity to assign to
     * @param string|string[] $roleNames The name of the role or roles to assign
     * @throws RoleNotFoundException Thrown if the input rule names do not exist
     */
    public function assignRoles($subjectId, $roleNames): void;

    /**
     * Creates a role
     *
     * @param string $roleName The name of the role
     * @return Role Creates a role
     */
    public function createRole(string $roleName): Role;

    /**
     * Deletes a role
     *
     * @param string $roleName The name of the role to delete
     */
    public function deleteRole(string $roleName): void;

    /**
     * Gets all roles for a subject
     *
     * @param int|string $subjectId The subject identity whose roles we want
     * @return Role[] The list of roles
     */
    public function getRolesForSubject($subjectId): array;

    /**
     * Gets the list of subject identities with the role
     *
     * @param string $roleName The name of the role to search for
     * @return array The list of subject identities with the role
     */
    public function getSubjectIdsWithRole(string $roleName): array;

    /**
     * Removes all roles from a subject
     *
     * @param int|string $subjectId The primary identity of the subject to remove
     */
    public function removeAllRolesFromSubject($subjectId): void;

    /**
     * Removes roles from a subject
     *
     * @param int|string $subjectId The identity of the subject to remove
     * @param string|string[] $roleNames The name of the role or roles to remove
     */
    public function removeRolesFromSubject($subjectId, $roleNames): void;

    /**
     * Gets whether or not a role exists
     *
     * @param string $roleName The name of the role to search for
     * @return bool True if the role exists, otherwise false
     */
    public function roleExists(string $roleName): bool;

    /**
     * Gets whether or not a subject has a role
     *
     * @param int|string $subjectId The Id of the subject
     * @param string $roleName The name of the role to search for
     * @return bool True if the subject has the role, otherwise false
     */
    public function subjectHasRole($subjectId, string $roleName): bool;
}
