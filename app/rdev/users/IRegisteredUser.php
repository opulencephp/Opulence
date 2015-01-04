<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for a registered user (eg someone you could log in as)
 */
namespace RDev\Users;

interface IRegisteredUser extends IUser
{
    /**
     * Gets the username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Sets the username
     *
     * @param string $username The username to use
     */
    public function setUsername($username);
} 