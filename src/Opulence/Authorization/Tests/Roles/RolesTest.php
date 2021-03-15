<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authorization\Tests\Roles;

use InvalidArgumentException;
use Opulence\Authorization\Roles\Orm\IRoleMembershipRepository;
use Opulence\Authorization\Roles\Orm\IRoleRepository;
use Opulence\Authorization\Roles\Role;
use Opulence\Authorization\Roles\RoleMembership;
use Opulence\Authorization\Roles\Roles;

/**
 * Tests the roles
 */
class RolesTest extends \PHPUnit\Framework\TestCase
{
    /** @var Roles The roles to use in tests */
    private $roles = null;
    /** @var IRoleRepository|\PHPUnit_Framework_MockObject_MockObject The role repository to use in tests */
    private $roleRepository = null;
    /** @var IRoleMembershipRepository|\PHPUnit_Framework_MockObject_MockObject The role membership repository to use in tests */
    private $roleMembershipRepository = null;

    /**
     * Tests up the tests
     */
    public function setUp()
    {
        $this->roleRepository = $this->createMock(IRoleRepository::class);
        $this->roleMembershipRepository = $this->createMock(IRoleMembershipRepository::class);
        $this->roles = new Roles($this->roleRepository, $this->roleMembershipRepository);
    }

    /**
     * Tests that assigning a non-existent role throws an exception
     */
    public function testAssigningNonExistentRoleThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->roles->assignRoles(1, 'foo');
    }

    /**
     * Tests checking for existing role
     */
    public function testCheckingForExistingRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(new Role(1, 'foo'));
        $this->assertTrue($this->roles->roleExists('foo'));
    }

    /**
     * Tests checking for non-existent role
     */
    public function testCheckingForNonExistentRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->assertFalse($this->roles->roleExists('foo'));
    }

    /**
     * Tests that creating a role adds it to the repository
     */
    public function testCreatingRoleAddsToRepository()
    {
        $role = new Role(-1, 'foo');
        $this->roleRepository->expects($this->once())
            ->method('add')
            ->with($role);
        $this->assertEquals($role, $this->roles->createRole('foo'));
    }

    /**
     * Tests deleting an existing role
     */
    public function testDeletingExistingRole()
    {
        $role = new Role(1, 'foo');
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn($role);
        $this->roles->deleteRole('foo');
    }

    /**
     * Tests deleting a non-existent role
     */
    public function testDeletingNonExistentRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->roles->deleteRole('foo');
    }

    /**
     * Tests getting the user Ids with a non-existent role
     */
    public function testGettingUserIdsWithNonExistentRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->assertEquals([], $this->roles->getSubjectIdsWithRole('foo'));
    }

    /**
     * Tests getting the user Ids with a role
     */
    public function testGettingUserIdsWithRole()
    {
        $memberships = [
            new RoleMembership(1, 2, new Role(3, 'foo')),
            new RoleMembership(2, 4, new Role(3, 'foo'))
        ];
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(new Role(3, 'foo'));
        $this->roleMembershipRepository->expects($this->once())
            ->method('getByRoleId')
            ->with(3)
            ->willReturn($memberships);
        $this->assertEquals([2, 4], $this->roles->getSubjectIdsWithRole('foo'));
    }

    /**
     * Tests that a membership is added on assignment
     */
    public function testMembershipIsAddedOnAssignment()
    {
        $role = new Role(3, 'foo');
        $membership = new RoleMembership(-1, 2, $role);
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn($role);
        $this->roleMembershipRepository->expects($this->once())
            ->method('add')
            ->with($membership);
        $this->roles->assignRoles(2, 'foo');
    }

    /**
     * Tests all removing roles from a user
     */
    public function testRemovingAllRolesFromUser()
    {
        $memberships = [
            new RoleMembership(1, 2, new Role(3, 'foo')),
            new RoleMembership(4, 2, new Role(5, 'bar')),
            new RoleMembership(6, 2, new Role(7, 'baz'))
        ];
        $this->roleMembershipRepository->expects($this->at(0))
            ->method('getBySubjectId')
            ->with(2)
            ->willReturn($memberships);
        $this->roleMembershipRepository->expects($this->at(1))
            ->method('delete')
            ->with($memberships[0]);
        $this->roleMembershipRepository->expects($this->at(2))
            ->method('delete')
            ->with($memberships[1]);
        $this->roleMembershipRepository->expects($this->at(3))
            ->method('delete')
            ->with($memberships[2]);
        $this->roles->removeAllRolesFromSubject(2);
    }

    /**
     * Tests removing roles from a user
     */
    public function testRemovingRolesFromUser()
    {
        $memberships = [
            new RoleMembership(1, 2, new Role(3, 'foo')),
            new RoleMembership(4, 2, new Role(5, 'bar')),
            new RoleMembership(6, 2, new Role(7, 'baz'))
        ];
        $this->roleMembershipRepository->expects($this->at(0))
            ->method('getBySubjectId')
            ->with(2)
            ->willReturn($memberships);
        $this->roleMembershipRepository->expects($this->at(1))
            ->method('delete')
            ->with($memberships[0]);
        $this->roleMembershipRepository->expects($this->at(2))
            ->method('delete')
            ->with($memberships[1]);
        $this->roles->removeRolesFromSubject(2, ['foo', 'bar']);
    }

    /**
     * Tests that a user does not have a non-existent role
     */
    public function testUserDoesNotHaveNonExistentRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->assertFalse($this->roles->subjectHasRole(2, 'foo'));
    }

    /**
     * Tests that a user does not have a role
     */
    public function testUserDoesNotHaveRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(new Role(3, 'foo'));
        $this->roleMembershipRepository->expects($this->once())
            ->method('getBySubjectAndRoleId')
            ->with(2, 3)
            ->willReturn(null);
        $this->assertFalse($this->roles->subjectHasRole(2, 'foo'));
    }

    /**
     * Tests that a user has a role
     */
    public function testUserHasRole()
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(new Role(3, 'foo'));
        $this->roleMembershipRepository->expects($this->once())
            ->method('getBySubjectAndRoleId')
            ->with(2, 3)
            ->willReturn([new RoleMembership(1, 2, new Role(3, 'foo'))]);
        $this->assertTrue($this->roles->subjectHasRole(2, 'foo'));
    }

    /**
     * Tests that the user roles are returned
     */
    public function testUserRolesAreReturned()
    {
        $membership = new RoleMembership(1, 2, new Role(3, 'foo'));
        $this->roleMembershipRepository->expects($this->once())
            ->method('getBySubjectId')
            ->with(2)
            ->willReturn([$membership]);
        $this->assertEquals([$membership->getRole()], $this->roles->getRolesForSubject(2));
    }
}
