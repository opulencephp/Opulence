<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
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
    public function assignRoles($userId, $roleNames)
    {
        foreach ((array)$roleNames as $roleName) {
            if (($role = $this->roleRepository->getByName($roleName)) === null) {
                throw new InvalidArgumentException("No role with name \"$roleName\" exists");
            }

            $membership = new RoleMembership(-1, $userId, $role);
            $this->roleMembershipRepository->add($membership);
        }

        // Todo:  Where do we commit UoW?  Here?  Outside?
    }

    /**
     * @inheritdoc
     */
    public function createRole(string $roleName) : Role
    {
        // TODO: Implement createRole() method.
    }

    /**
     * @inheritdoc
     */
    public function deleteRole(string $roleName)
    {
        // TODO: Implement deleteRole() method.
    }

    /**
     * @inheritdoc
     */
    public function getRolesForUser($userId) : array
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritdoc
     */
    public function getUserIdsWithRole(string $roleName) : array
    {
        // TODO: Implement getUserIdsWithRole() method.
    }

    /**
     * @inheritdoc
     */
    public function removeRolesFromUser($userId, $roleNames)
    {
        // TODO: Implement removeRolesFromUser() method.
    }

    /**
     * @inheritdoc
     */
    public function roleExists(string $roleName) : bool
    {
        // TODO: Implement roleExists() method.
    }

    /**
     * @inheritdoc
     */
    public function userHasRole($userId, string $roleName) : bool
    {
        // TODO: Implement userHasRole() method.
    }
}