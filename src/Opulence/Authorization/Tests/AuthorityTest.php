<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authorization\Tests;

use Opulence\Authorization\Authority;
use Opulence\Authorization\IAuthority;
use Opulence\Authorization\Permissions\PermissionRegistry;
use Opulence\Authorization\Roles\IRoles;

/**
 * Tests the authority
 */
class AuthorityTest extends \PHPUnit\Framework\TestCase
{
    /** @var Authority The authority to use in tests */
    private $authority = null;
    /** @var PermissionRegistry The registry to use in tests */
    private $permissionRegistry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->permissionRegistry = new PermissionRegistry();
        $this->roles = $this->createMock(IRoles::class);
        $this->authority = new Authority(23, ['foo', 'bar'], $this->permissionRegistry);
    }

    /**
     * Tests can returns false when callback returns false
     */
    public function testFalseCallback()
    {
        $this->permissionRegistry->registerCallback('foo', function () {
            return false;
        });
        $this->assertFalse($this->authority->can('foo'));
        $this->assertTrue($this->authority->cannot('foo'));
    }

    /**
     * Tests that forUser creates a new instance
     */
    public function testForUserCreatesNewInstance()
    {
        $forUserInstance = $this->authority->forSubject(1, []);
        $this->assertInstanceOf(IAuthority::class, $forUserInstance);
        $this->assertNotSame($forUserInstance, $this->authority);
    }

    /**
     * Tests can returns false when user is does not have role
     */
    public function testNoRoles()
    {
        $this->permissionRegistry->registerRoles('foo', 'bar');
        $this->assertFalse($this->authority->can('baz'));
        $this->assertTrue($this->authority->cannot('baz'));
    }

    /**
     * Tests that an override is used
     */
    public function testOverrideUsed()
    {
        $this->permissionRegistry->registerOverrideCallback(function ($userId, string $permission, $argument) {
            $this->assertEquals(23, $userId);
            $this->assertEquals('foo', $permission);
            $this->assertEquals('bar', $argument);

            return true;
        });
        $this->permissionRegistry->registerCallback('foo', function () {
            return false;
        });
        $this->assertTrue($this->authority->can('foo', 'bar'));
        $this->assertFalse($this->authority->cannot('foo', 'bar'));
    }

    /**
     * Tests can returns true when callback returns true
     */
    public function testTrueCallback()
    {
        $this->permissionRegistry->registerCallback('foo', function () {
            return true;
        });
        $this->assertTrue($this->authority->can('foo'));
        $this->assertFalse($this->authority->cannot('foo'));
    }

    /**
     * Tests can returns true when user has role
     */
    public function testWithRoles()
    {
        $this->permissionRegistry->registerRoles('foo', 'bar');
        $this->assertTrue($this->authority->can('foo'));
        $this->assertFalse($this->authority->cannot('foo'));
    }
}
