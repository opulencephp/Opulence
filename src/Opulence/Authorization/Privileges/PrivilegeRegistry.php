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
use Opulence\Authorization\IAuthorizable;

/**
 * Defines the privilege registry
 */
class PrivilegeRegistry implements IPrivilegeRegistry
{
    /** @var callable[] The mapping of privileges to callbacks */
    private $privilegeCallbacks = [];
    /** @var array The mapping of privileges to user roles */
    private $privilegesToRoles = [];

    /**
     * @inheritdoc
     */
    public function getCallback(string $privilege) : callable
    {
        // Default to callbacks
        if (isset($this->privilegeCallbacks[$privilege])) {
            return $this->privilegeCallbacks[$privilege];
        }

        // Fall back to using roles
        if (isset($this->privilegesToRoles[$privilege])) {
            return function (IAuthorizable $user) use ($privilege) {
                return count(array_intersect($user->getRoles(), $this->privilegesToRoles[$privilege])) > 0;
            };
        }

        throw new InvalidArgumentException("No callback nor roles registered for privilege \"$privilege\"");
    }

    /**
     * @inheritdoc
     */
    public function getRoles(string $privilege) : array
    {
        if (!isset($this->privilegesToRoles[$privilege])) {
            return [];
        }

        return $this->privilegesToRoles[$privilege];
    }

    /**
     * @inheritdoc
     */
    public function registerCallback(string $privilege, callable $callback)
    {
        $this->privilegeCallbacks[$privilege] = $callback;
    }

    /**
     * @inheritdoc
     */
    public function registerRoles(string $privilege, $roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];

        if (!isset($this->privilegesToRoles[$privilege])) {
            $this->privilegesToRoles[$privilege] = [];
        }

        $this->privilegesToRoles[$privilege] = array_merge($this->privilegesToRoles[$privilege], $roles);
    }
}