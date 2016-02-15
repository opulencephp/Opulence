<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization;

use Opulence\Authorization\Permissions\IPermissionRegistry;
use Opulence\Authorization\Roles\IRoles;

/**
 * Defines the authority
 */
class Authority implements IAuthority
{
    /** @var int|string The Id of the current user */
    protected $userId = -1;
    /** @var IPermissionRegistry The permission registry */
    protected $permissionRegistry = null;
    /** @var IRoles The roles */
    protected $roles = null;

    /**
     * @param int|string $userId The Id of the current user
     * @param IPermissionRegistry $permissionRegistry The permission registry
     * @param IRoles $roles The roles
     */
    public function __construct($userId, IPermissionRegistry $permissionRegistry, IRoles $roles)
    {
        $this->userId = $userId;
        $this->permissionRegistry = $permissionRegistry;
        $this->roles = $roles;
    }

    /**
     * @inheritdoc
     */
    public function can(string $permission, ...$arguments) : bool
    {
        // Check the overrides first
        foreach ($this->permissionRegistry->getOverrideCallbacks() as $overrideCallback) {
            if (call_user_func($overrideCallback, $this->userId, $permission, ...$arguments)) {
                return true;
            }
        }

        $requiredRoles = $this->permissionRegistry->getRoles($permission);

        // If our user has at least one of the required roles
        if (
            $requiredRoles !== null
            && count(array_intersect($requiredRoles, $this->roles->getRolesForUser($this->userId))) > 0
        ) {
            return true;
        }

        if (($callback = $this->permissionRegistry->getCallback($permission)) === null) {
            return false;
        }

        return call_user_func($callback, $this->userId, ...$arguments);
    }

    /**
     * @inheritdoc
     */
    public function cannot(string $permission, ...$arguments) : bool
    {
        return !$this->can($permission, ...$arguments);
    }

    /**
     * @inheritdoc
     */
    public function forUser($userId) : IAuthority
    {
        return new self($userId, $this->permissionRegistry, $this->roles);
    }
}