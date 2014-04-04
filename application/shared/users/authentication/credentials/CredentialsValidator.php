<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for validating credentials
 */
namespace RamODev\Application\Shared\Users\Authentication\Credentials;
use RamODev\Application\Shared\Users\Authentication\Credentials\Exceptions;

class CredentialsValidator implements ICredentialsValidator
{
    /**
     * Gets whether or not the input credentials are valid
     *
     * @param ICredentials $credentials The credentials to validate
     * @return bool True if the credentials are valid, otherwise false
     * @throws Exceptions\TamperedCredentialsException Thrown if we suspect the credentials of having been tampered with
     */
    public function credentialsAreValid(ICredentials $credentials)
    {
        return false;
    }
} 