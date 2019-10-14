<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\TestsTemp\Roles;

use InvalidArgumentException;
use Opulence\Authorization\Roles\Orm\IRoleMembershipRepository;
use Opulence\Authorization\Roles\Orm\IRoleRepository;
use Opulence\Authorization\Roles\Role;
use Opulence\Authorization\Roles\RoleMembership;
use Opulence\Authorization\Roles\Roles;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the roles
 */
class RolesTest extends \PHPUnit\Framework\TestCase
{
    private Roles $roles;
    /** @var IRoleRepository|MockObject The role repository to use in tests */
    private IRoleRepository $roleRepository;
    /** @var IRoleMembershipRepository|MockObject The role membership repository to use in tests */
    private IRoleMembershipRepository $roleMembershipRepository;

    protected function setUp(): void
    {
        $this->roleRepository = $this->createMock(IRoleRepository::class);
        $this->roleMembershipRepository = $this->createMock(IRoleMembershipRepository::class);
        $this->roles = new Roles($this->roleRepository, $this->roleMembershipRepository);
    }

    /**
     * Tests that assigning a non-existent role throws an exception
     */
    public function testAssigningNonExistentRoleThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->roles->assignRoles(1, 'foo');
    }

    public function testCheckingForExistingRole(): void
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
    public function testCheckingForNonExistentRole(): void
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->assertFalse($this->roles->roleExists('foo'));
    }

    public function testCreatingRoleAddsToRepository(): void
    {
        $role = new Role(-1, 'foo');
        $this->roleRepository->expects($this->once())
            ->method('add')
            ->with($role);
        $this->assertEquals($role, $this->roles->createRole('foo'));
    }

    public function testDeletingExistingRole(): void
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
    public function testDeletingNonExistentRole(): void
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
    public function testGettingUserIdsWithNonExistentRole(): void
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->assertEquals([], $this->roles->getSubjectIdsWithRole('foo'));
    }

    public function testGettingUserIdsWithRole(): void
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

    public function testMembershipIsAddedOnAssignment(): void
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

    public function testRemovingAllRolesFromUser(): void
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

    public function testRemovingRolesFromUser(): void
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
    public function testUserDoesNotHaveNonExistentRole(): void
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(null);
        $this->assertFalse($this->roles->subjectHasRole(2, 'foo'));
    }

    public function testUserDoesNotHaveRole(): void
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

    public function testUserHasRole(): void
    {
        $this->roleRepository->expects($this->once())
            ->method('getByName')
            ->with('foo')
            ->willReturn(new Role(3, 'foo'));
        $this->roleMembershipRepository->expects($this->once())
            ->method('getBySubjectAndRoleId')
            ->with(2, 3)
            ->willReturn(new RoleMembership(1, 2, new Role(3, 'foo')));
        $this->assertTrue($this->roles->subjectHasRole(2, 'foo'));
    }

    public function testUserRolesAreReturned(): void
    {
        $membership = new RoleMembership(1, 2, new Role(3, 'foo'));
        $this->roleMembershipRepository->expects($this->once())
            ->method('getBySubjectId')
            ->with(2)
            ->willReturn([$membership]);
        $this->assertEquals([$membership], $this->roles->getRolesForSubject(2));
    }
}
