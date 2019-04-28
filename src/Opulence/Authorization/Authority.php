<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization;

use Opulence\Authorization\Permissions\IPermissionRegistry;

/**
 * Defines the authority
 */
class Authority implements IAuthority
{
    /** @var int|string The primary identity of the current subject */
    protected $subjectId;
    /** @var array The list of roles the subject has */
    protected $subjectRoles;
    /** @var IPermissionRegistry The permission registry */
    protected $permissionRegistry;

    /**
     * @param int|string $subjectId The primary identity of the current subject
     * @param array $subjectRoles The list of roles the subject has
     * @param IPermissionRegistry $permissionRegistry The permission registry
     */
    public function __construct($subjectId, array $subjectRoles, IPermissionRegistry $permissionRegistry)
    {
        $this->setSubject($subjectId, $subjectRoles);
        $this->permissionRegistry = $permissionRegistry;
    }

    /**
     * @inheritdoc
     */
    public function can(string $permission, ...$arguments): bool
    {
        // Check the overrides first
        foreach ($this->permissionRegistry->getOverrideCallbacks() as $overrideCallback) {
            if ($overrideCallback($this->subjectId, $permission, ...$arguments)) {
                return true;
            }
        }

        $requiredRoles = $this->permissionRegistry->getRoles($permission);

        // If our subject has at least one of the required roles
        if ($requiredRoles !== null && count(array_intersect($requiredRoles, $this->subjectRoles)) > 0) {
            return true;
        }

        if (($callback = $this->permissionRegistry->getCallback($permission)) === null) {
            return false;
        }

        return $callback($this->subjectId, ...$arguments);
    }

    /**
     * @inheritdoc
     */
    public function cannot(string $permission, ...$arguments): bool
    {
        return !$this->can($permission, ...$arguments);
    }

    /**
     * @inheritdoc
     */
    public function forSubject($subjectId, array $subjectRoles = null): IAuthority
    {
        return new self($subjectId, $subjectRoles, $this->permissionRegistry);
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subjectId, array $subjectRoles): void
    {
        $this->subjectId = $subjectId;
        $this->subjectRoles = $subjectRoles;
    }
}
