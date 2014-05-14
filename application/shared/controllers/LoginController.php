<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RamODev\Application\Shared\Controllers;
use RamODev\Application\Shared\Models\Cryptography\Repositories\Token;
use RamODev\Application\Shared\Models\Users\Authentication\Credentials;
use RamODev\Application\Shared\Models\Users\Authentication\Credentials\Factories;
use RamODev\Application\Shared\Models\Users\Repositories\User;
use RamODev\Application\Shared\Models\Web;
use RamODev\Application\TBA\Models\Configs;

class LoginController
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

        $loginCredentials = $this->loginCredentialsFactory->createLoginCredentials(
            $user->getId(),
            new \DateTime("now", new \DateTimeZone("UTC")),
            new \DateTime("+1 week", new \DateTimeZone("UTC"))
        );
        $tokenValue = $loginCredentials->getLoginToken()
            ->generateRandomString(Configs\Authentication::LOGIN_TOKEN_LENGTH);
        $this->tokenRepo->add(
            $loginCredentials->getLoginToken(),
            $this->tokenRepo->hashToken(
                $tokenValue,
                Configs\Authentication::LOGIN_TOKEN_HASH_ALGORITHM,
                Configs\Authentication::LOGIN_TOKEN_HASH_COST
            ));

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
        return $this->tokenRepo->deactivate($loginCredentials->getLoginToken());
    }
} 