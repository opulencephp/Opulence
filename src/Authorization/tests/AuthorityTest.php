<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authorization\tests;

use Opulence\Authorization\Authority;
use Opulence\Authorization\IAuthority;
use Opulence\Authorization\Permissions\PermissionRegistry;
use Opulence\Authorization\Roles\IRoles;

/**
 * Tests the authority
 */
class AuthorityTest extends \PHPUnit\Framework\TestCase
{
    private Authority $authority;
    private PermissionRegistry $permissionRegistry;

    protected function setUp(): void
    {
        $this->permissionRegistry = new PermissionRegistry();
        $this->roles = $this->createMock(IRoles::class);
        $this->authority = new Authority(23, ['foo', 'bar'], $this->permissionRegistry);
    }

    public function testFalseCallback(): void
    {
        $this->permissionRegistry->registerCallback('foo', fn () => false);
        $this->assertFalse($this->authority->can('foo'));
        $this->assertTrue($this->authority->cannot('foo'));
    }

    public function testForUserCreatesNewInstance(): void
    {
        $forUserInstance = $this->authority->forSubject(1, []);
        $this->assertInstanceOf(IAuthority::class, $forUserInstance);
        $this->assertNotSame($forUserInstance, $this->authority);
    }

    public function testNoRoles(): void
    {
        $this->permissionRegistry->registerRoles('foo', 'bar');
        $this->assertFalse($this->authority->can('baz'));
        $this->assertTrue($this->authority->cannot('baz'));
    }

    public function testOverrideUsed(): void
    {
        $this->permissionRegistry->registerOverrideCallback(function ($userId, string $permission, $argument) {
            $this->assertEquals(23, $userId);
            $this->assertEquals('foo', $permission);
            $this->assertEquals('bar', $argument);

            return true;
        });
        $this->permissionRegistry->registerCallback('foo', fn () => false);
        $this->assertTrue($this->authority->can('foo', 'bar'));
        $this->assertFalse($this->authority->cannot('foo', 'bar'));
    }

    public function testTrueCallback(): void
    {
        $this->permissionRegistry->registerCallback('foo', fn () => true);
        $this->assertTrue($this->authority->can('foo'));
        $this->assertFalse($this->authority->cannot('foo'));
    }

    public function testWithRoles(): void
    {
        $this->permissionRegistry->registerRoles('foo', 'bar');
        $this->assertTrue($this->authority->can('foo'));
        $this->assertFalse($this->authority->cannot('foo'));
    }
}
