<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\Tests\Roles;

use Opulence\Authorization\Roles\Role;
use Opulence\Authorization\Roles\RoleMembership;

/**
 * Tests the role membership
 */
class RoleMembershipTest extends \PHPUnit\Framework\TestCase
{
    private RoleMembership $membership;
    private Role $role;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->role = new Role(1, 'foo');
        $this->membership = new RoleMembership(1, 2, $this->role);
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId(): void
    {
        $this->assertEquals(1, $this->membership->getId());
    }

    /**
     * Tests getting the role
     */
    public function testGettingRole(): void
    {
        $this->assertSame($this->role, $this->membership->getRole());
    }

    /**
     * Tests getting the user Id
     */
    public function testGettingUserId(): void
    {
        $this->assertEquals(2, $this->membership->getSubjectId());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId(): void
    {
        $this->membership->setId(23);
        $this->assertEquals(23, $this->membership->getId());
    }
}
