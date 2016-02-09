<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Privileges;

use InvalidArgumentException;

/**
 * Tests the privilege registry
 */
class PrivilegeRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var PrivilegeRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new PrivilegeRegistry();
    }

    /**
     * Tests callbacks take priority over roles
     */
    public function testCallbacksTakePriorityOverRoles()
    {
        $callback = function () {
            return false;
        };
        $this->registry->registerCallback("foo", $callback);
        $this->registry->registerRoles("foo", "bar");
        $this->assertSame($callback, $this->registry->getCallback("foo"));
    }

    /**
     * Tests empty array returned when no roles registered
     */
    public function testEmptyArrayReturnedWhenNoRolesRegistered()
    {
        $this->assertEquals([], $this->registry->getRoles("foo"));
    }

    /**
     * Tests that an exception is thrown when no rules nor callback are registered
     */
    public function testExceptionThrownWhenNoRolesNorCallbackRegistered()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->registry->getCallback("foo");
    }

    /**
     * Tests registering an array of roles
     */
    public function testRegisteringArrayOfRoles()
    {
        $this->registry->registerRoles("foo", ["bar", "baz"]);
        $this->assertEquals(["bar", "baz"], $this->registry->getRoles("foo"));
    }

    /**
     * Tests registering a callback
     */
    public function testRegisteringCallback()
    {
        $callback = function () {
            return false;
        };
        $this->registry->registerCallback("foo", $callback);
        $this->assertSame($callback, $this->registry->getCallback("foo"));
    }

    /**
     * Tests registering a single role
     */
    public function testRegisteringSingleRole()
    {
        $this->registry->registerRoles("foo", "bar");
        $this->assertEquals(["bar"], $this->registry->getRoles("foo"));
    }

    /**
     * Tests roles are not overwritten when double registering privilege
     */
    public function testRolesNotOverwrittenWhenDoubleRegisteringPrivilege()
    {
        $this->registry->registerRoles("foo", "bar");
        $this->registry->registerRoles("foo", "baz");
        $this->assertEquals(["bar", "baz"], $this->registry->getRoles("foo"));
    }
}