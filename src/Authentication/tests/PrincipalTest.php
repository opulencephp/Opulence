<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\tests;

use Opulence\Authentication\Principal;

/**
 * Tests the principal
 */
class PrincipalTest extends \PHPUnit\Framework\TestCase
{
    private Principal $principal;

    protected function setUp(): void
    {
        $this->principal = new Principal('foo', 'bar', ['baz']);
    }

    public function testCheckingRoles(): void
    {
        $this->assertTrue($this->principal->hasRole('baz'));
        $this->assertFalse($this->principal->hasRole('doesNotExist'));
    }

    public function testGettingId(): void
    {
        $this->assertEquals('bar', $this->principal->getId());
    }

    public function testGettingRoles(): void
    {
        $this->assertEquals(['baz'], $this->principal->getRoles());
    }

    public function testGettingType(): void
    {
        $this->assertEquals('foo', $this->principal->getType());
    }
}
