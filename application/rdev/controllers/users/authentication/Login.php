<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login controller
 */
namespace RDev\Controllers\Users\Authentication;
use RDev\Controllers;
use RDev\Models\Authentication\Credentials;
use RDev\Models\Authentication\Credentials\ORM\Credential;
use RDev\Models\Authentication\EntityTypes;
use RDev\Models\Cryptography;
use RDev\Models\Exceptions\Log;
use RDev\Models\HTTP;
use RDev\Models\Users\ORM\User;

class Login extends Controllers\Controller
{
    /** @var Cryptography\IHasher The hasher to use for tokens */
    private $hasher = null;
    /** @var Credentials\ICredentials The credentials */
    private $credentials = null;
    /** @var User\IUserRepo The user repo to use for finding users */
    private $userRepo = null;
    /** @var Credential\IRepo The credential repo to use for getting/adding user credentials */
    private $credentialRepo = null;

    /**
     * @param Cryptography\IHasher $hasher The hasher to use for tokens
     * @param Credentials\ICredentials $credentials The credentials
     * @param User\IUserRepo $userRepo The user repo to use for finding users
     * @param Credential\IRepo $credentialRepo The credential repo to use for getting/adding user credentials
     */
    public function __construct(Cryptography\IHasher $hasher, Credentials\ICredentials $credentials, User\IUserRepo $userRepo,
                                Credential\IRepo $credentialRepo)
    {
        $this->hasher = $hasher;
        $this->userRepo = $userRepo;
        $this->credentialRepo = $credentialRepo;
    }

    /**
     * Authenticates the current user
     *
     * @return HTTP\Response The response of the authentication attempt
     */
    public function authenticate()
    {
        // Todo: Make this a route filter
        if(!$this->credentials->has(Credentials\CredentialTypes::LOGIN))
        {
            return new HTTP\RedirectResponse("/login", HTTP\ResponseHeaders::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Attempts to log in as a user
     *
     * @param string $username The username to log in with
     * @param string $unhashedPassword The unhashed password to log in with
     * @return HTTP\Response The response from the action
     */
    public function logIn($username, $unhashedPassword)
    {
        try
        {
            $user = $this->userRepo->getByUsernameAndPassword($username, $unhashedPassword);

            if($user === null)
            {
                return new HTTP\RedirectResponse("/login", HTTP\ResponseHeaders::HTTP_UNAUTHORIZED);
            }

            $tokenValue = $this->hasher->generateRandomString(64);
            $token = new Cryptography\Token(
                -1,
                $this->hasher->generate($tokenValue),
                new \DateTime("now", new \DateTimeZone("UTC")),
                new \DateTime("+1 week", new \DateTimeZone("UTC")),
                true
            );
            $loginCredential = new Credentials\Credential(-1, Credentials\CredentialTypes::LOGIN, EntityTypes::USER,
                $user->getId(), $token);
            $this->credentialRepo->add($loginCredential);
            $this->credentialRepo->getUnitOfWork()->commit();
            $this->credentials->save($loginCredential, $tokenValue);

            return new HTTP\RedirectResponse("/home");
        }
        catch(\Exception $ex)
        {
            Log::write("Failed to log in user: " . $ex);

            return new HTTP\RedirectResponse("/login", HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logs a user out
     *
     * @return HTTP\Response The response from the action
     */
    public function logOut()
    {
        $loginCredential = $this->credentials->get(Credentials\CredentialTypes::LOGIN);
        $this->credentialRepo->delete($loginCredential);
        $this->credentials->delete($loginCredential->getTypeId());
        $this->credentialRepo->getUnitOfWork()->commit();

        return new HTTP\RedirectResponse("/login");
    }
} 