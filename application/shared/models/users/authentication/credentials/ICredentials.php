<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for credentials to implement
 */
namespace RamODev\Application\Shared\Models\Users\Authentication\Credentials;

interface ICredentials
{
    /**
     * Gets the Id of the user whose credentials these are
     *
     * @return int The Id of the user whose credentials these are
     */
    public function getUserId();
} 