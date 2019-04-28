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

/**
 * Tests the role
 */
class RoleTest extends \PHPUnit\Framework\TestCase
{
    /** @var Role The role to use in tests */
    private $role = null;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->role = new Role(1, 'foo');
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId(): void
    {
        $this->assertEquals(1, $this->role->getId());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName(): void
    {
        $this->assertEquals('foo', $this->role->getName());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId(): void
    {
        $this->role->setId(23);
        $this->assertEquals(23, $this->role->getId());
    }
}
