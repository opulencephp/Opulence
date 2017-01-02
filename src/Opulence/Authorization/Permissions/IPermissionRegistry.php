<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Permissions;

/**
 * Defines the interface for permission registries to implement
 */
interface IPermissionRegistry
{
    /**
     * Gets the list of callbacks that return whether or not a subject has a permission
     *
     * @param string $permission The permission to search for
     * @return callable|null The callback if one was found, otherwise null
     */
    public function getCallback(string $permission);

    /**
     * Gets the list of callbacks that are evaluated before any permissions
     *
     * @return callable[] The list of override callbacks
     */
    public function getOverrideCallbacks() : array;

    /**
     * Gets the list of roles that have the input permission
     *
     * @param string $permission The permission to search for
     * @return array|null The list of roles if any were found, otherwise null
     */
    public function getRoles(string $permission);

    /**
     * Registers a callback to be evaluated for a permission
     *
     * @param string $permission The permission being registered
     * @param callable $callback The callback that will be evaluated (subject identity must be first argument)
     */
    public function registerCallback(string $permission, callable $callback);

    /**
     * Registers a callback to be evaluated before considering any permissions
     *
     * @param callable $callback The callback that will be evaluated (subject identity must be first argument, permission second)
     */
    public function registerOverrideCallback(callable $callback);

    /**
     * Registers a permission for certain roles
     *
     * @param string $permission The permission being registered
     * @param string|array The role name or list of role names that have the input permission
     */
    public function registerRoles(string $permission, $roles);
}