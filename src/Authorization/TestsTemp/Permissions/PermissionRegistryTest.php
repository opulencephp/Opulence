<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\TestsTemp\Permissions;

use Opulence\Authorization\Permissions\PermissionRegistry;

/**
 * Tests the permission registry
 */
class PermissionRegistryTest extends \PHPUnit\Framework\TestCase
{
    private PermissionRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new PermissionRegistry();
    }

    public function testEmptyArrayReturnedWhenNoOverrideCallbacksRegistered(): void
    {
        $this->assertEquals([], $this->registry->getOverrideCallbacks());
    }

    public function testEmptyArrayReturnedWhenNoRolesRegistered(): void
    {
        $this->assertNull($this->registry->getRoles('foo'));
    }

    public function testNullReturnedWhenNoCallbackRegistered(): void
    {
        $this->assertNull($this->registry->getCallback('foo'));
    }

    public function testRegisteringArrayOfRoles(): void
    {
        $this->registry->registerRoles('foo', ['bar', 'baz']);
        $this->assertEquals(['bar', 'baz'], $this->registry->getRoles('foo'));
    }

    public function testRegisteringCallback(): void
    {
        $callback = fn () => false;
        $this->registry->registerCallback('foo', $callback);
        $this->assertSame($callback, $this->registry->getCallback('foo'));
    }

    public function testRegisteringOverride(): void
    {
        $override = fn () => true;
        $this->registry->registerOverrideCallback($override);
        $this->assertSame([$override], $this->registry->getOverrideCallbacks());
    }

    public function testRegisteringSingleRole(): void
    {
        $this->registry->registerRoles('foo', 'bar');
        $this->assertEquals(['bar'], $this->registry->getRoles('foo'));
    }

    public function testRolesNotOverwrittenWhenDoubleRegisteringPermission(): void
    {
        $this->registry->registerRoles('foo', 'bar');
        $this->registry->registerRoles('foo', 'baz');
        $this->assertEquals(['bar', 'baz'], $this->registry->getRoles('foo'));
    }
}
