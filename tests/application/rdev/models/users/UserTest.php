<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the user object
 */
namespace RDev\Models\Users;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var User The user object we're going to clone for our tests */
    private $prototypicalUser = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->prototypicalUser = new User(18175, "foo", new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")),
            [1, 2, 3]
        );
    }

    /**
     * Tests checking for a role that a user doesn't have
     */
    public function testCheckingForRoleThatUserDoesntHave()
    {
        $user = $this->getClonedUser();
        $this->assertFalse($user->hasRole(998877));
    }

    /**
     * Tests verifying that a user has a role
     */
    public function testCheckingForRoleThatUserHas()
    {
        $user = $this->getClonedUser();
        $this->assertTrue($user->hasRole(1));
    }

    /**
     * Test getting the creation date
     */
    public function testGettingDateCreated()
    {
        $user = $this->getClonedUser();
        $this->assertEquals(new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), $user->getDateCreated());
    }

    /**
     * Test getting the Id
     */
    public function testGettingId()
    {
        $user = $this->getClonedUser();
        $this->assertEquals(18175, $user->getId());
    }

    /**
     * Tests getting the user's roles
     */
    public function testGettingRoles()
    {
        $user = $this->getClonedUser();
        $this->assertEquals([1, 2, 3], $user->getRoles());
    }

    /**
     * Test getting the username
     */
    public function testGettingUsername()
    {
        $user = $this->getClonedUser();
        $this->assertEquals("foo", $user->getUsername());
    }

    /**
     * Test setting the Id
     */
    public function testSettingId()
    {
        $user = $this->getClonedUser();
        $user->setId(12345);
        $this->assertEquals(12345, $user->getId());
    }

    /**
     * Clones the prototypical user and returns it
     *
     * @return User The user object to use for testing
     */
    private function getClonedUser()
    {
        return clone $this->prototypicalUser;
    }
} 