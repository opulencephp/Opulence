<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests;

use Opulence\Authentication\Principal;

/**
 * Tests the principal
 */
class PrincipalTest extends \PHPUnit\Framework\TestCase
{
    private Principal $principal;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->principal = new Principal('foo', 'bar', ['baz']);
    }

    /**
     * Tests checking if a principal has roles
     */
    public function testCheckingRoles(): void
    {
        $this->assertTrue($this->principal->hasRole('baz'));
        $this->assertFalse($this->principal->hasRole('doesNotExist'));
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId(): void
    {
        $this->assertEquals('bar', $this->principal->getId());
    }

    /**
     * Tests getting the roles
     */
    public function testGettingRoles(): void
    {
        $this->assertEquals(['baz'], $this->principal->getRoles());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType(): void
    {
        $this->assertEquals('foo', $this->principal->getType());
    }
}
