<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Privileges;

/**
 * Defines the interface for privilege registries to implement
 */
interface IPrivilegeRegistry
{
    /**
     * Gets the list of callbacks that return whether or not a user has a privilege
     *
     * @param string $privilege The privilege to search for
     * @return callable|null The callback if one was found, otherwise null
     */
    public function getCallback(string $privilege);

    /**
     * Gets the list of roles that have the input privilege
     *
     * @param string $privilege The privilege to search for
     * @return array|null The list of roles if any were found, otherwise null
     */
    public function getRoles(string $privilege);

    /**
     * Registers a callback to be evaluated for a privilege
     *
     * @param string $privilege The privilege being registered
     * @param callable $callback The callback that will be evaluated
     */
    public function registerCallback(string $privilege, callable $callback);

    /**
     * Registers a privilege for certain roles
     *
     * @param string $privilege The privilege being registered
     * @param array|mixed $roles The role or roles with the input privilege
     */
    public function registerRoles(string $privilege, $roles);
}