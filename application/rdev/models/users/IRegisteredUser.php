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
     * Gets a user's hashed password
     *
     * @return string The hashed password
     */
    public function getHashedPassword();

    /**
     * Sets a user's hashed password, which is suitable for storage
     *
     * @param string $hashedPassword The hashed password
     */
    public function setHashedPassword($hashedPassword);
} 