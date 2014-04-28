<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the user object
 */
namespace RamODev\Application\Shared\Users;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var User The user object we're going to clone for our tests */
    private $protypicalUser = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->protypicalUser = new User(18175, "foo@bar.com", "fakepassword", "foo@bar.com", new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), "David", "Young");
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
     * Test getting the email
     */
    public function testGettingEmail()
    {
        $user = $this->getClonedUser();
        $this->assertEquals("foo@bar.com", $user->getEmail());
    }

    /**
     * Test getting the first name
     */
    public function testGettingFirstName()
    {
        $user = $this->getClonedUser();
        $this->assertEquals("David", $user->getFirstName());
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
     * Test getting the last name
     */
    public function testGettingLastName()
    {
        $user = $this->getClonedUser();
        $this->assertEquals("Young", $user->getLastName());
    }

    /**
     * Test getting the password
     */
    public function testGettingPassword()
    {
        $user = $this->getClonedUser();
        $this->assertEquals("fakepassword", $user->getHashedPassword());
    }

    /**
     * Test getting the username
     */
    public function testGettingUsername()
    {
        $user = $this->getClonedUser();
        $this->assertEquals("foo@bar.com", $user->getUsername());
    }

    /**
     * Test setting the email
     */
    public function testSettingEmail()
    {
        $user = $this->getClonedUser();
        $user->setEmail("bar@foo.com");
        $this->assertEquals("bar@foo.com", $user->getEmail());
    }

    /**
     * Test setting the first name
     */
    public function testSettingFirstName()
    {
        $user = $this->getClonedUser();
        $user->setFirstName("Brian");
        $this->assertEquals("Brian", $user->getFirstName());
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
     * Test setting the last name
     */
    public function testSettingLastName()
    {
        $user = $this->getClonedUser();
        $user->setLastName("Banjo");
        $this->assertEquals("Banjo", $user->getLastName());
    }

    /**
     * Test setting the password
     */
    public function testSettingPassword()
    {
        $user = $this->getClonedUser();
        $user->setHashedPassword("newfakepassword");
        $this->assertEquals("newfakepassword", $user->getHashedPassword());
    }

    /**
     * Clones the protypical user and returns it
     *
     * @return User The user object to use for testing
     */
    private function getClonedUser()
    {
        return clone $this->protypicalUser;
    }
} 