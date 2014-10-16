<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the registered user
 */
namespace RDev\Models\Users;

class RegisteredUserTest extends \PHPUnit_Framework_TestCase
{
    /** @var RegisteredUser The user to use in tests */
    private $user = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $dateCreated = new \DateTime("now");
        $this->user = new RegisteredUser(1, "dave", "fooPassword", $dateCreated, []);
    }

    /**
     * Tests getting the hashed password after setting it in the constructor
     */
    public function testGettingHashedPasswordAfterSettingInConstructor()
    {
        $this->assertEquals("fooPassword", $this->user->getHashedPassword());
    }

    /**
     * Tests setting the hashed password
     */
    public function testSettingHashedPassword()
    {
        $this->user->setHashedPassword("newFooPassword");
        $this->assertEquals("newFooPassword", $this->user->getHashedPassword());
    }
} 