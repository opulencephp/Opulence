<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for credentials to implement
 */
namespace RamODev\Application\Users\Authentication\Credentials;

interface ICredentials
{
    /**
     * Gets the ID of the user whose credentials these are
     *
     * @return int The ID of the user whose credentials these are
     */
    public function getUserID();
} 