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
     * Tests getting the login token
     */
    public function testGettingLoginToken()
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $loginToken = new Cryptography\Token(1, $now, $now);
        $credentials = new LoginCredentials(24, $loginToken);
        $this->assertEquals($loginToken, $credentials->getLoginToken());
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
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $oldLoginToken = new Cryptography\Token(1, $now, $now);
        $newLoginToken = new Cryptography\Token(2, $now, $now);
        $credentials = new LoginCredentials(24, $oldLoginToken);
        $credentials->setLoginToken($newLoginToken);
        $this->assertEquals($newLoginToken, $credentials->getLoginToken());
    }
} 