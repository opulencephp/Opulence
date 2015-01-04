<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the session class
 */
namespace RDev\Sessions;
use RDev\Authentication\Credentials;
use RDev\Authentication;
use RDev\Users;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests not setting the credentials in the constructor
     */
    public function testNotSettingCredentialsInConstructor()
    {
        $session = new Session();
        $user = new Users\GuestUser();
        $credentials = new Credentials\Credentials($user->getId(), Authentication\EntityTypes::USER);
        $this->assertEquals($credentials, $session->getCredentials());
    }

    /**
     * Tests not setting the user in the constructor
     */
    public function testNotSettingUserInConstructor()
    {
        $session = new Session();
        $this->assertEquals(new Users\GuestUser(), $session->getUser());
    }

    /**
     * Tests setting the credentials
     */
    public function testSettingCredentials()
    {
        $session = new Session();
        $credentials = new Credentials\Credentials();
        $session->setCredentials($credentials);
        $this->assertSame($credentials, $session->getCredentials());
    }

    /**
     * Tests setting the user
     */
    public function testSettingUser()
    {
        $session = new Session();
        $user = new Users\User(1, new \DateTime("now"), []);
        $session->setUser($user);
        $this->assertSame($user, $session->getUser());
    }
} 