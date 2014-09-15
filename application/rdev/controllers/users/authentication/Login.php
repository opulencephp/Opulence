<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login controller
 */
namespace RDev\Users\Authentication\Controllers;
use RDev\Models\Cryptography;
use RDev\Models\Cryptography\ORM\Token;
use RDev\Models\Users\Authentication\Credentials;
use RDev\Models\Users\Authentication\Credentials\Factories;
use RDev\Models\Users\ORM\User;
use RDev\Models\Web;
use TBA\Models\Configs;

class Login
{
    /** @var User\IUserRepo The user repo to use for finding users */
    private $userRepo = null;
    /** @var Token\ITokenRepo The token repo to use for logging in users */
    private $tokenRepo = null;
    /** @var Factories\LoginCredentialsFactory $loginCredentialsFactory The factory to use to create credentials */
    private $loginCredentialsFactory = null;
    /** @var Web\HTTP The HTTP object to use in our requests/responses */
    private $http = null;

    /**
     * @param User\IUserRepo $userRepo The user repo to use for finding users
     * @param Token\ITokenRepo $tokenRepo The token repo to use for logging in users
     * @param Factories\LoginCredentialsFactory $loginCredentialsFactory The factory to use to create credentials
     * @param Web\HTTP $http The HTTP object to use in our requests/responses
     */
    public function __construct(User\IUserRepo $userRepo, Token\ITokenRepo $tokenRepo,
                                Factories\LoginCredentialsFactory $loginCredentialsFactory, Web\HTTP $http)
    {
        $this->userRepo = $userRepo;
        $this->tokenRepo = $tokenRepo;
        $this->loginCredentialsFactory = $loginCredentialsFactory;
        $this->http = $http;
    }

    /**
     * Attempts to log in as a user
     *
     * @param string $username The username to log in with
     * @param string $unhashedPassword The unhashed password to log in with
     * @return bool True if successful, otherwise false
     */
    public function logIn($username, $unhashedPassword)
    {
        $user = $this->userRepo->getByUsernameAndPassword($username, $unhashedPassword);

        if($user === false)
        {
            return false;
        }

        $tokenValue = Cryptography\Token::generateRandomString(Configs\Authentication::LOGIN_TOKEN_LENGTH);
        $loginToken = new Cryptography\Token(
            -1,
            Cryptography\TokenTypes::LOGIN,
            $user->getId(),
            Cryptography\Token::generateHashedValue(
                $tokenValue,
                Configs\Authentication::LOGIN_TOKEN_HASH_ALGORITHM,
                Configs\Authentication::LOGIN_TOKEN_HASH_COST,
                Configs\Authentication::TOKEN_PEPPER
            ),
            new \DateTime("now", new \DateTimeZone("UTC")),
            new \DateTime("+1 week", new \DateTimeZone("UTC")),
            true
        );
        $loginCredentials = new Credentials\LoginCredentials($user->getId(), $loginToken);
        $this->tokenRepo->add($loginCredentials->getLoginToken());

        setcookie("userId", $user, time() + 3600, "/", "", false, true);
        setcookie("loginTokenId", $loginCredentials->getLoginToken()->getId(), time() + 3600, "/", "", false, true);
        setcookie("loginTokenValue", $tokenValue, time() + 3600, "/", "", false, true);

        return true;
    }

    /**
     * Logs a user out
     *
     * @param Credentials\LoginCredentials $loginCredentials The credentials to deactivate
     * @return bool True if successful, otherwise false
     */
    public function logOut(Credentials\LoginCredentials $loginCredentials)
    {
        return $this->tokenRepo->delete($loginCredentials->getLoginToken());
    }
} 