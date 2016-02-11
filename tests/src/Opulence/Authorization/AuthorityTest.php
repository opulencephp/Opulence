<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization;

use Opulence\Authorization\Permissions\PermissionRegistry;
use Opulence\Authorization\Roles\IRoles;
use Opulence\Tests\Authorization\Mocks\User;

/**
 * Tests the authority
 */
class AuthorityTest extends \PHPUnit_Framework_TestCase
{
    /** @var Authority The authority to use in tests */
    private $authority = null;
    /** @var User The user to use in tests */
    private $user = null;
    /** @var PermissionRegistry The registry to use in tests */
    private $permissionRegistry = null;
    /** @var IRoles|\PHPUnit_Framework_MockObject_MockObject The roles to use in tests */
    private $roles = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->user = new User(23);
        $this->permissionRegistry = new PermissionRegistry();
        $this->roles = $this->getMock(IRoles::class);
        $this->authority = new Authority($this->user->getId(), $this->user, $this->permissionRegistry, $this->roles);
    }

    /**
     * Tests can returns false when callback returns false
     */
    public function testFalseCallback()
    {
        $this->permissionRegistry->registerCallback("foo", function () {
            return false;
        });
        $this->assertFalse($this->authority->can("foo"));
        $this->assertTrue($this->authority->cannot("foo"));
    }

    /**
     * Tests can returns false when user is does not have role
     */
    public function testNoRoles()
    {
        $this->permissionRegistry->registerRoles("foo", "bar");
        $this->roles->expects($this->exactly(2))
            ->method("getRolesForUser")
            ->with($this->user->getId())
            ->willReturn(["baz"]);
        $this->assertFalse($this->authority->can("foo"));
        $this->assertTrue($this->authority->cannot("foo"));
    }

    /**
     * Tests can returns true when callback returns true
     */
    public function testTrueCallback()
    {
        $this->permissionRegistry->registerCallback("foo", function () {
            return true;
        });
        $this->assertTrue($this->authority->can("foo"));
        $this->assertFalse($this->authority->cannot("foo"));
    }

    /**
     * Tests can returns true when user has role
     */
    public function testWithRoles()
    {
        $this->permissionRegistry->registerRoles("foo", "bar");
        $this->roles->expects($this->exactly(2))
            ->method("getRolesForUser")
            ->with($this->user->getId())
            ->willReturn(["bar"]);
        $this->assertTrue($this->authority->can("foo"));
        $this->assertFalse($this->authority->cannot("foo"));
    }
}