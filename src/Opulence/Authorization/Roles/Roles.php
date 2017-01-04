<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Roles;

use InvalidArgumentException;
use Opulence\Authorization\Roles\Orm\IRoleMembershipRepository;
use Opulence\Authorization\Roles\Orm\IRoleRepository;

/**
 * Defines the role manager
 */
class Roles implements IRoles
{
    /** @var IRoleRepository The role repository */
    protected $roleRepository = null;
    /** @var IRoleMembershipRepository The role membership repository */
    protected $roleMembershipRepository = null;

    /**
     * @param IRoleRepository $roleRepository The role repository
     * @param IRoleMembershipRepository $roleMembershipRepository The role membership repository
     */
    public function __construct(IRoleRepository $roleRepository, IRoleMembershipRepository $roleMembershipRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->roleMembershipRepository = $roleMembershipRepository;
    }

    /**
     * @inheritdoc
     */
    public function assignRoles($subjectId, $roleNames)
    {
        foreach ((array)$roleNames as $roleName) {
            if (($role = $this->roleRepository->getByName($roleName)) === null) {
                throw new InvalidArgumentException("No role with name \"$roleName\" exists");
            }

            $membership = new RoleMembership(-1, $subjectId, $role);
            $this->roleMembershipRepository->add($membership);
        }
    }

    /**
     * @inheritdoc
     */
    public function createRole(string $roleName) : Role
    {
        $role = new Role(-1, $roleName);
        $this->roleRepository->add($role);

        return $role;
    }

    /**
     * @inheritdoc
     */
    public function deleteRole(string $roleName)
    {
        if (($role = $this->roleRepository->getByName($roleName)) !== null) {
            $this->roleRepository->delete($role);
        }
    }

    /**
     * @inheritdoc
     */
    public function getRolesForSubject($subjectId) : array
    {
        return $this->roleMembershipRepository->getBySubjectId($subjectId);
    }

    /**
     * @inheritdoc
     */
    public function getSubjectIdsWithRole(string $roleName) : array
    {
        if (($role = $this->roleRepository->getByName($roleName)) === null) {
            return [];
        }

        $subjectIds = [];

        foreach ($this->roleMembershipRepository->getByRoleId($role->getId()) as $membership) {
            $subjectIds[] = $membership->getSubjectId();
        }

        return $subjectIds;
    }

    /**
     * @inheritdoc
     */
    public function removeAllRolesFromSubject($subjectId)
    {
        // Pass membership by reference because delete() accepts references
        foreach ($this->roleMembershipRepository->getBySubjectId($subjectId) as &$membership) {
            $this->roleMembershipRepository->delete($membership);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeRolesFromSubject($subjectId, $roleNames)
    {
        $roleNames = (array)$roleNames;

        // Pass membership by reference because delete() accepts references
        foreach ($this->roleMembershipRepository->getBySubjectId($subjectId) as &$membership) {
            if (in_array($membership->getRole()->getName(), $roleNames)) {
                $this->roleMembershipRepository->delete($membership);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function roleExists(string $roleName) : bool
    {
        return $this->roleRepository->getByName($roleName) !== null;
    }

    /**
     * @inheritdoc
     */
    public function subjectHasRole($subjectId, string $roleName) : bool
    {
        if (($role = $this->roleRepository->getByName($roleName)) === null) {
            return false;
        }

        return $this->roleMembershipRepository->getBySubjectAndRoleId($subjectId, $role->getId()) !== null;
    }
}
