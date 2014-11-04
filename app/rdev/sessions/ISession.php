<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for sessions to implement
 */
namespace RDev\Sessions;
use RDev\Authentication\Credentials;
use RDev\Users;

interface ISession
{
    /**
     * Gets the current user's credentials
     *
     * @return Credentials\ICredentials The current user's credentials
     */
    public function getCredentials();

    /**
     * Gets the current user
     *
     * @return Users\IUser The current user
     */
    public function getUser();

    /**
     * Sets the current user's credentials
     *
     * @param Credentials\ICredentials $credentials The credentials to use
     */
    public function setCredentials(Credentials\ICredentials $credentials);

    /**
     * Sets the current user
     *
     * @param Users\IUser $user The user to use
     */
    public function setUser(Users\IUser $user);
} 