<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the session class
 */
namespace RDev\Sessions;
use DateTime;
use RDev\Authentication\Credentials\CredentialCollection;
use RDev\Authentication\EntityTypes;
use RDev\Users\GuestUser;
use RDev\Users\User;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests not setting the credentials in the constructor
     */
    public function testNotSettingCredentialsInConstructor()
    {
        $session = new Session();
        $user = new GuestUser();
        $credentials = new CredentialCollection($user->getId(), EntityTypes::USER);
        $this->assertEquals($credentials, $session->getCredentials());
    }

    /**
     * Tests not setting the user in the constructor
     */
    public function testNotSettingUserInConstructor()
    {
        $session = new Session();
        $this->assertEquals(new GuestUser(), $session->getUser());
    }

    /**
     * Tests setting the credentials
     */
    public function testSettingCredentials()
    {
        $session = new Session();
        $credentials = new CredentialCollection();
        $session->setCredentials($credentials);
        $this->assertSame($credentials, $session->getCredentials());
    }

    /**
     * Tests setting the user
     */
    public function testSettingUser()
    {
        $session = new Session();
        $user = new User(1, new DateTime("now"), []);
        $session->setUser($user);
        $this->assertSame($user, $session->getUser());
    }
} 