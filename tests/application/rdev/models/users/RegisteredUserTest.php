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
        $this->user = new RegisteredUser(1, "dave", 321, $dateCreated, []);
    }

    /**
     * Tests getting the password Id after setting it in the constructor
     */
    public function testGettingPasswordIdAfterSettingInConstructor()
    {
        $this->assertEquals(321, $this->user->getPasswordId());
    }

    /**
     * Tests setting the password Id
     */
    public function testSettingHashedPassword()
    {
        $this->user->setPasswordId(846);
        $this->assertEquals(846, $this->user->getPasswordId());
    }
} 