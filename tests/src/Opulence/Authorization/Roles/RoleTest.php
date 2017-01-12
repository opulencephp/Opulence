<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authorization\Roles;

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
    public function setUp()
    {
        $this->role = new Role(1, 'foo');
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals(1, $this->role->getId());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals('foo', $this->role->getName());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $this->role->setId(23);
        $this->assertEquals(23, $this->role->getId());
    }
}
