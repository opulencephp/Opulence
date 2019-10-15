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
use PHPUnit\Framework\TestCase;

/**
 * Tests the role
 */
class RoleTest extends TestCase
{
    /** @var Role The role to use in tests */
    private Role $role;

    protected function setUp(): void
    {
        $this->role = new Role(1, 'foo');
    }

    public function testGettingId(): void
    {
        $this->assertEquals(1, $this->role->getId());
    }

    public function testGettingName(): void
    {
        $this->assertEquals('foo', $this->role->getName());
    }

    public function testSettingId(): void
    {
        $this->role->setId(23);
        $this->assertEquals(23, $this->role->getId());
    }
}
