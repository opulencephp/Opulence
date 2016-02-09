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
    public function getCallback(string $privilege)
    {
        if (!isset($this->privilegeCallbacks[$privilege])) {
            return null;
        }

        return $this->privilegeCallbacks[$privilege];
    }

    /**
     * @inheritdoc
     */
    public function getRoles(string $privilege)
    {
        if (!isset($this->privilegesToRoles[$privilege])) {
            return null;
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