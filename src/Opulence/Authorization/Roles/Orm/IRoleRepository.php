<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Roles\Orm;

use Opulence\Authorization\Roles\Role;

/**
 * Defines the interface for role repositories to implement
 */
interface IRoleRepository
{
    /**
     * Adds a role
     *
     * @param Role $role The role to add
     */
    public function add($role);

    /**
     * Deletes a role
     *
     * @param Role $role The role to delete
     */
    public function delete($role);

    /**
     * Gets all the roles
     *
     * @return Role[] The list of all the roles
     */
    public function getAll() : array;

    /**
     * Gets the role with the input Id
     *
     * @param int|string $id The Id of the role we're searching for
     * @return Role The role with the input Id
     */
    public function getById($id);

    /**
     * Gets the role with the input name
     *
     * @param string $name The name of the role we're searching for
     * @return Role|null The role with the input name if one exists, otherwise null
     */
    public function getByName(string $name);
}
