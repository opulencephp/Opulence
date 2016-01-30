<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users;

use DateTimeImmutable;

/**
 * Tests the user object
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var User The user object we're going to clone for our tests */
    private $user = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->user = new User(18175, "foo", new DateTimeImmutable("1776-07-04 12:34:56"), [1, 2, 3]);
    }

    /**
     * Tests checking for a role that a user doesn't have
     */
    public function testCheckingForRoleThatUserDoesNotHave()
    {
        $this->assertFalse($this->user->hasRole(998877));
    }

    /**
     * Tests verifying that a user has a role
     */
    public function testCheckingForRoleThatUserHas()
    {
        $this->assertTrue($this->user->hasRole(1));
    }

    /**
     * Test getting the creation date
     */
    public function testGettingDateCreated()
    {
        $this->assertEquals(new DateTimeImmutable("1776-07-04 12:34:56"), $this->user->getDateCreated());
    }

    /**
     * Tests getting the hashed password
     */
    public function testGettingHashedPassword()
    {
        $this->assertEquals("foo", $this->user->getHashedPassword());
    }

    /**
     * Test getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals(18175, $this->user->getId());
    }

    /**
     * Tests getting the user's roles
     */
    public function testGettingRoles()
    {
        $this->assertEquals([1, 2, 3], $this->user->getRoles());
    }

    /**
     * Tests not setting any roles in constructor
     */
    public function testNotSettingRolesInConstructor()
    {
        $user = new User(18175, "foo", new DateTimeImmutable());
        $this->assertEquals([], $user->getRoles());
    }

    /**
     * Tests setting the hashed password
     */
    public function testSettingHashedPassword()
    {
        $this->user->setHashedPassword("bar");
        $this->assertEquals("bar", $this->user->getHashedPassword());
    }

    /**
     * Test setting the Id
     */
    public function testSettingId()
    {
        $this->user->setId(12345);
        $this->assertEquals(12345, $this->user->getId());
    }
} 