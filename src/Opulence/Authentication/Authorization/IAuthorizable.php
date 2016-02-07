<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Authorization;

/**
 * Defines the interface for authorizable entities to implement
 */
interface IAuthorizable
{
    /**
     * Gets the list of this entity's roles
     *
     * @return array The list of rules
     */
    public function getRoles() : array;

    /**
     * Gets whether or not an entity has a particular role
     *
     * @param mixed $role The role to search for
     * @return bool True if the entity has the role, otherwise false
     */
    public function hasRole($role) : bool;
}