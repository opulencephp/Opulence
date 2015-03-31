<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for sessions to implement
 */
namespace RDev\Sessions;
use RDev\Authentication\Credentials\ICredentialCollection;
use RDev\Users\IUser;

interface ISession
{
    /**
     * Gets the current user's credentials
     *
     * @return ICredentialCollection The current user's credentials
     */
    public function getCredentials();

    /**
     * Gets the current user
     *
     * @return IUser The current user
     */
    public function getUser();

    /**
     * Sets the current user's credentials
     *
     * @param ICredentialCollection $credentials The credentials to use
     */
    public function setCredentials(ICredentialCollection $credentials);

    /**
     * Sets the current user
     *
     * @param IUser $user The user to use
     */
    public function setUser(IUser $user);
} 