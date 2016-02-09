<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Privileges;

use InvalidArgumentException;

/**
 * Defines the interface for privilege registries to implement
 */
interface IPrivilegeRegistry
{
    /**
     * Gets the list of roles that have the input privilege
     *
     * @param string $privilege The privilege to search for
     * @return callable The callback
     * @throws InvalidArgumentException Thrown if no callback nor roles were registered for the privilege
     */
    public function getCallback(string $privilege) : callable;

    /**
     * Gets the list of roles that have the input privilege
     *
     * @param string $privilege The privilege to search for
     * @return array The list of roles
     */
    public function getRoles(string $privilege) : array;

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