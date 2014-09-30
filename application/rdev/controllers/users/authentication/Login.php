<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login controller
 */
namespace RDev\Users\Authentication\Controllers;
use RDev\Models\Authentication\Credentials;
use RDev\Models\Authentication\Credentials\ORM\Credential;
use RDev\Models\Authentication\EntityTypes;
use RDev\Models\Cryptography;
use RDev\Models\Exceptions\Log;
use RDev\Models\Users\ORM\User;
use RDev\Models\Web;
use TBA\Models\Configs;

class Login
{
    /** @var User\IUserRepo The user repo to use for finding users */
    private $userRepo = null;
    /** @var Credential\IRepo The credential repo to use for getting/adding user credentials */
    private $credentialRepo = null;

    /**
     * @param User\IUserRepo $userRepo The user repo to use for finding users
     * @param Credential\IRepo $credentialRepo The credential repo to use for getting/adding user credentials
     */
    public function __construct(User\IUserRepo $userRepo, Credential\IRepo $credentialRepo)
    {
        $this->userRepo = $userRepo;
        $this->credentialRepo = $credentialRepo;
    }

    /**
     * Authenticates the current user
     *
     * @return bool True if the user is authenticated, otherwise false
     */
    public function authenticate()
    {
        return $this->credentials->get(Credentials\CredentialTypes::LOGIN) instanceof Credentials\ICredential;
    }

    /**
     * Attempts to log in as a user
     *
     * @param string $username The username to log in with
     * @param string $unhashedPassword The unhashed password to log in with
     * @return int The login result constant indicating the result
     */
    public function logIn($username, $unhashedPassword)
    {
        try
        {
            $user = $this->userRepo->getByUsernameAndPassword($username, $unhashedPassword);

            if($user === null)
            {
                return LoginResults::USER_NOT_FOUND;
            }

            $tokenValue = Cryptography\Token::generateRandomString(Configs\Authentication::LOGIN_TOKEN_LENGTH);
            $token = new Cryptography\Token(
                -1,
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
            $loginCredential = new Credentials\Credential(-1, Credentials\CredentialTypes::LOGIN, EntityTypes::USER,
                $user->getId(), $token);
            $this->credentialRepo->add($loginCredential);
            // TODO:  Commit UoW
            $this->credentials->save($loginCredential);

            return LoginResults::SUCCESSFUL;
        }
        catch(\Exception $ex)
        {
            Log::write("Failed to log in user: " . $ex);

            return LoginResults::EXCEPTION;
        }
    }

    /**
     * Logs a user out
     */
    public function logOut()
    {
        $loginCredential = $this->credentials->get(Credentials\CredentialTypes::LOGIN);
        $this->credentialRepo->delete($loginCredential);
        $this->credentials->delete($loginCredential);
    }
} 