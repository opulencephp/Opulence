<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a session that persists throughout a transaction on a page
 */
namespace RDev\Sessions;
use RDev\Authentication\Credentials;
use RDev\Authentication;
use RDev\Users;

class Session implements ISession
{
    /** @var Users\IUser The current user */
    private $user = null;
    /** @var Credentials\ICredentials The current user's credentials */
    private $credentials = null;

    /**
     * @param Users\IUser $user The current user
     * @param Credentials\ICredentials $credentials The current user's credentials
     */
    public function __construct(Users\IUser $user = null, Credentials\ICredentials $credentials = null)
    {
        if($user === null)
        {
            $user = new Users\GuestUser();
        }

        if($credentials === null)
        {
            $credentials = new Credentials\Credentials($user->getId(), Authentication\EntityTypes::USER);
        }

        $this->setUser($user);
        $this->setCredentials($credentials);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setCredentials(Credentials\ICredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(Users\IUser $user)
    {
        $this->user = $user;
    }
} 