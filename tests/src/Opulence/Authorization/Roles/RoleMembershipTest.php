<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Roles;

/**
 * Tests the role membership
 */
class RoleMembershipTest extends \PHPUnit_Framework_TestCase
{
    /** @var RoleMembership The role membership to use in tests */
    private $membership = null;
    /** @var Role The role to use in tests */
    private $role = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->role = new Role(1, "foo");
        $this->membership = new RoleMembership(1, 2, $this->role);
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals(1, $this->membership->getId());
    }

    /**
     * Tests getting the role
     */
    public function testGettingRole()
    {
        $this->assertSame($this->role, $this->membership->getRole());
    }

    /**
     * Tests getting the user Id
     */
    public function testGettingUserId()
    {
        $this->assertEquals(2, $this->membership->getUserId());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $this->membership->setId(23);
        $this->assertEquals(23, $this->membership->getId());
    }
}