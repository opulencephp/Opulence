<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for a registered user (eg someone you could log in as)
 */
namespace RDev\Models\Users;

interface IRegisteredUser extends IUser
{
    /**
     * Gets a user's password Id
     *
     * @return int|string The password Id
     */
    public function getPasswordId();

    /**
     * Sets a user's password Id
     *
     * @param int|string $passwordId The password Id
     */
    public function setPasswordId($passwordId);
} 