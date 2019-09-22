<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\Permissions;

/**
 * Defines the permission registry
 */
class PermissionRegistry implements IPermissionRegistry
{
    /** @var callable[] The list of override callbacks */
    protected array $overrideCallbacks = [];
    /** @var callable[] The mapping of permissions to callbacks */
    protected array $permissionCallbacks = [];
    /** @var array The mapping of permissions to subject roles */
    protected array $permissionsToRoles = [];

    /**
     * @inheritdoc
     */
    public function getCallback(string $permission): ?callable
    {
        if (!isset($this->permissionCallbacks[$permission])) {
            return null;
        }

        return $this->permissionCallbacks[$permission];
    }

    /**
     * @inheritdoc
     */
    public function getOverrideCallbacks(): array
    {
        return $this->overrideCallbacks;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(string $permission): ?array
    {
        if (!isset($this->permissionsToRoles[$permission])) {
            return null;
        }

        return $this->permissionsToRoles[$permission];
    }

    /**
     * @inheritdoc
     */
    public function registerCallback(string $permission, callable $callback): void
    {
        $this->permissionCallbacks[$permission] = $callback;
    }

    /**
     * @inheritdoc
     */
    public function registerOverrideCallback(callable $callback): void
    {
        $this->overrideCallbacks[] = $callback;
    }

    /**
     * @inheritdoc
     */
    public function registerRoles(string $permission, $roles): void
    {
        $roles = is_array($roles) ? $roles : [$roles];

        if (!isset($this->permissionsToRoles[$permission])) {
            $this->permissionsToRoles[$permission] = [];
        }

        $this->permissionsToRoles[$permission] = array_merge($this->permissionsToRoles[$permission], $roles);
    }
}
