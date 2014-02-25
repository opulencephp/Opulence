<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the credentials validator interface
 */
namespace RamODev\API\V1\Users\Authentication\Credentials;

interface ICredentialsValidator
{
    /**
     * Gets whether or not the input credentials are valid
     *
     * @param ICredentials $credentials The credentials to validate
     * @return bool True if the credentials are valid, otherwise false
     * @throws Exceptions\TamperedCredentialsException Thrown if we suspect the credentials of having been tampered with
     */
    public function credentialsAreValid(ICredentials $credentials);
} 