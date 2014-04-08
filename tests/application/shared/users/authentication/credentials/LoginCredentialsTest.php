<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the login credentials
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials;

class LoginCredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the expiration from the credentials
     */
    public function testGettingExpiration()
    {
        $expiration = new \DateTime("now", new \DateTimeZone("utc"));
        $credentials = new LoginCredentials(24, "foo", $expiration);
        $this->assertEquals($expiration, $credentials->getExpiration());
    }

    /**
     * Tests getting the hashed token from the credentials
     */
    public function testGettingHashedToken()
    {
        $hashedToken = "foo";
        $credentials = new LoginCredentials(24, $hashedToken, new \DateTime("now", new \DateTimeZone("utc")));
        $this->assertEquals($hashedToken, $credentials->getHashedToken());
    }

    /**
     * Tests getting the user ID from the credentials
     */
    public function testGettingUserID()
    {
        $userID = 24;
        $credentials = new LoginCredentials($userID, "foo", new \DateTime("now", new \DateTimeZone("utc")));
        $this->assertEquals($userID, $credentials->getUserID());
    }

    /**
     * Tests setting the hasehd token
     */
    public function testSettingHashedToken()
    {
        $hashedToken = "foo";
        $credentials = new LoginCredentials(24, "", new \DateTime("now", new \DateTimeZone("utc")));
        $credentials->setHashedToken($hashedToken);
        $this->assertEquals($hashedToken, $credentials->getHashedToken());
    }
} 