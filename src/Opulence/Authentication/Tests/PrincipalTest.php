<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests;

use Opulence\Authentication\Principal;

/**
 * Tests the principal
 */
class PrincipalTest extends \PHPUnit\Framework\TestCase
{
    /** @var Principal The principal to use in tests */
    private $principal = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->principal = new Principal('foo', 'bar', ['baz']);
    }

    /**
     * Tests checking if a principal has roles
     */
    public function testCheckingRoles()
    {
        $this->assertTrue($this->principal->hasRole('baz'));
        $this->assertFalse($this->principal->hasRole('doesNotExist'));
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals('bar', $this->principal->getId());
    }

    /**
     * Tests getting the roles
     */
    public function testGettingRoles()
    {
        $this->assertEquals(['baz'], $this->principal->getRoles());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType()
    {
        $this->assertEquals('foo', $this->principal->getType());
    }
}
