<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization;

use Opulence\Authorization\Privileges\PrivilegeRegistry;

/**
 * Tests the authority
 */
class AuthorityTest extends \PHPUnit_Framework_TestCase
{
    /** @var Authority The authority to use in tests */
    private $authority = null;
    /** @var IAuthorizable|\PHPUnit_Framework_MockObject_MockObject The user to use in tests */
    private $user = null;
    /** @var PrivilegeRegistry The registry to use in tests */
    private $privilegeRegistry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->user = $this->getMock(IAuthorizable::class);
        $this->privilegeRegistry = new PrivilegeRegistry();
        $this->authority = new Authority($this->user, $this->privilegeRegistry);
    }

    /**
     * Tests can returns false when callback returns false
     */
    public function testCanReturnsFalseWhenCallbackReturnsFalse()
    {
        $this->privilegeRegistry->registerCallback("foo", function () {
            return false;
        });
        $this->assertFalse($this->authority->can("foo"));
    }

    /**
     * Tests can returns false when user is does not have role
     */
    public function testCanReturnsFalseWhenUserDoesNotHaveRole()
    {
        $this->privilegeRegistry->registerRoles("foo", "bar");
        $this->user->expects($this->once())
            ->method("getRoles")
            ->willReturn(["baz"]);
        $this->assertFalse($this->authority->can("foo"));
    }

    /**
     * Tests can returns true when callback returns true
     */
    public function testCanReturnsTrueWhenCallbackReturnsTrue()
    {
        $this->privilegeRegistry->registerCallback("foo", function () {
            return true;
        });
        $this->assertTrue($this->authority->can("foo"));
    }

    /**
     * Tests can returns true when user has role
     */
    public function testCanReturnsTrueWhenUserHasRole()
    {
        $this->privilegeRegistry->registerRoles("foo", "bar");
        $this->user->expects($this->once())
            ->method("getRoles")
            ->willReturn(["bar"]);
        $this->assertTrue($this->authority->can("foo"));
    }
}