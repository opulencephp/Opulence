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
use PHPUnit\Framework\TestCase;

/**
 * Tests the role membership
 */
class RoleMembershipTest extends TestCase
{
    private RoleMembership $membership;
    private Role $role;

    protected function setUp(): void
    {
        $this->role = new Role(1, 'foo');
        $this->membership = new RoleMembership(1, 2, $this->role);
    }

    public function testGettingId(): void
    {
        $this->assertEquals(1, $this->membership->getId());
    }

    public function testGettingRole(): void
    {
        $this->assertSame($this->role, $this->membership->getRole());
    }

    public function testGettingUserId(): void
    {
        $this->assertEquals(2, $this->membership->getSubjectId());
    }

    public function testSettingId(): void
    {
        $this->membership->setId(23);
        $this->assertEquals(23, $this->membership->getId());
    }
}
