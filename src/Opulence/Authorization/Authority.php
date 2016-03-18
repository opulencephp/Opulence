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
    /** @var int|string The primary identity of the current subject */
    protected $primaryIdentity = -1;
    /** @var IPermissionRegistry The permission registry */
    protected $permissionRegistry = null;
    /** @var IRoles The roles */
    protected $roles = null;

    /**
     * @param int|string $primaryIdentity The primary identity of the current subject
     * @param IPermissionRegistry $permissionRegistry The permission registry
     * @param IRoles $roles The roles
     */
    public function __construct($primaryIdentity, IPermissionRegistry $permissionRegistry, IRoles $roles)
    {
        $this->setPrimaryIdentity($primaryIdentity);
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
            if (call_user_func($overrideCallback, $this->primaryIdentity, $permission, ...$arguments)) {
                return true;
            }
        }

        $requiredRoles = $this->permissionRegistry->getRoles($permission);

        // If our subject has at least one of the required roles
        if (
            $requiredRoles !== null
            && count(array_intersect($requiredRoles, $this->roles->getRolesForSubject($this->primaryIdentity))) > 0
        ) {
            return true;
        }

        if (($callback = $this->permissionRegistry->getCallback($permission)) === null) {
            return false;
        }

        return call_user_func($callback, $this->primaryIdentity, ...$arguments);
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
    public function forSubject($primaryIdentity) : IAuthority
    {
        return new self($primaryIdentity, $this->permissionRegistry, $this->roles);
    }

    /**
     * @inheritdoc
     */
    public function setPrimaryIdentity($primaryIdentity)
    {
        $this->primaryIdentity = $primaryIdentity;
    }
}