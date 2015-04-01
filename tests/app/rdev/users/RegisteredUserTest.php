<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the registered user
 */
namespace RDev\Users;
use DateTime;

class RegisteredUserTest extends \PHPUnit_Framework_TestCase
{
    /** @var RegisteredUser The user to use in tests */
    private $user = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->user = new RegisteredUser(1, "dave", new DateTime("now"), []);
    }

    /**
     * Tests getting the username after setting it in the constructor
     */
    public function testGettingUsernameAfterSettingInConstructor()
    {
        $this->assertEquals("dave", $this->user->getUsername());
    }

    /**
     * Tests setting the username
     */
    public function testSettingUsername()
    {
        $this->user->setUsername("foo");
        $this->assertEquals("foo", $this->user->getUsername());
    }
} 