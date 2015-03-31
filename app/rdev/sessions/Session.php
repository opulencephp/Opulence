<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a session that persists throughout a transaction on a page
 */
namespace RDev\Sessions;
use RDev\Authentication\Credentials\CredentialCollection;
use RDev\Authentication\Credentials\ICredentialCollection;
use RDev\Authentication;
use RDev\Users\GuestUser;
use RDev\Users\IUser;

class Session implements ISession
{
    /** @var IUser The current user */
    private $user = null;
    /** @var ICredentialCollection The current user's credentials */
    private $credentials = null;

    /**
     * @param IUser $user The current user
     * @param ICredentialCollection $credentials The current user's credentials
     */
    public function __construct(IUser $user = null, ICredentialCollection $credentials = null)
    {
        if($user === null)
        {
            $user = new GuestUser();
        }

        if($credentials === null)
        {
            $credentials = new CredentialCollection($user->getId(), Authentication\EntityTypes::USER);
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
    public function setCredentials(ICredentialCollection $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(IUser $user)
    {
        $this->user = $user;
    }
} 