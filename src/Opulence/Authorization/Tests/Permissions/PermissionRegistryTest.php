<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authorization\Tests\Permissions;

use Opulence\Authorization\Permissions\PermissionRegistry;

/**
 * Tests the permission registry
 */
class PermissionRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var PermissionRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new PermissionRegistry();
    }

    /**
     * Tests getting override callbacks when none registered
     */
    public function testEmptyArrayReturnedWhenNoOverrideCallbacksRegistered()
    {
        $this->assertEquals([], $this->registry->getOverrideCallbacks());
    }

    /**
     * Tests null returned when no roles registered
     */
    public function testEmptyArrayReturnedWhenNoRolesRegistered()
    {
        $this->assertNull($this->registry->getRoles('foo'));
    }

    /**
     * Tests that null is returned when no callback is registered
     */
    public function testNullReturnedWhenNoCallbackRegistered()
    {
        $this->assertNull($this->registry->getCallback('foo'));
    }

    /**
     * Tests registering an array of roles
     */
    public function testRegisteringArrayOfRoles()
    {
        $this->registry->registerRoles('foo', ['bar', 'baz']);
        $this->assertEquals(['bar', 'baz'], $this->registry->getRoles('foo'));
    }

    /**
     * Tests registering a callback
     */
    public function testRegisteringCallback()
    {
        $callback = function () {
            return false;
        };
        $this->registry->registerCallback('foo', $callback);
        $this->assertSame($callback, $this->registry->getCallback('foo'));
    }

    /**
     * Tests registering an override
     */
    public function testRegisteringOverride()
    {
        $override = function () {
            return true;
        };
        $this->registry->registerOverrideCallback($override);
        $this->assertSame([$override], $this->registry->getOverrideCallbacks());
    }

    /**
     * Tests registering a single role
     */
    public function testRegisteringSingleRole()
    {
        $this->registry->registerRoles('foo', 'bar');
        $this->assertEquals(['bar'], $this->registry->getRoles('foo'));
    }

    /**
     * Tests roles are not overwritten when double registering permission
     */
    public function testRolesNotOverwrittenWhenDoubleRegisteringPermission()
    {
        $this->registry->registerRoles('foo', 'bar');
        $this->registry->registerRoles('foo', 'baz');
        $this->assertEquals(['bar', 'baz'], $this->registry->getRoles('foo'));
    }
}
