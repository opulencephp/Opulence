<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the login credentials
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Cryptography;

class LoginCredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the token
     */
    public function testGettingLoginToken()
    {
        $token = new Cryptography\Token(1, "foo", new \DateTime("now", new \DateTimeZone("utc")),
            new \DateTime("now", new \DateTimeZone("utc")));
        $credentials = new LoginCredentials(24, $token);
        $this->assertEquals($token, $credentials->getLoginToken());
    }

    /**
     * Tests getting the user Id from the credentials
     */
    public function testGettingUserId()
    {
        $userId = 24;
        $token = new Cryptography\Token(1, "foo", new \DateTime("now", new \DateTimeZone("utc")),
            new \DateTime("now", new \DateTimeZone("utc")));
        $credentials = new LoginCredentials($userId, $token);
        $this->assertEquals($userId, $credentials->getUserId());
    }

    /**
     * Tests setting the login token
     */
    public function testSettingLoginToken()
    {
        $oldToken = new Cryptography\Token(1, "foo", new \DateTime("now", new \DateTimeZone("utc")),
            new \DateTime("now", new \DateTimeZone("utc")));
        $newToken = new Cryptography\Token(2, "bar", new \DateTime("now", new \DateTimeZone("utc")),
            new \DateTime("now", new \DateTimeZone("utc")));
        $credentials = new LoginCredentials(24, $oldToken);
        $credentials->setLoginToken($newToken);
        $this->assertEquals($newToken, $credentials->getLoginToken());
    }
} 