<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for validating credentials
 */
namespace RamODev\Application\API\V1\Users\Authentication\Credentials;
use RamODev\Application\API\V1\Users\Authentication\Credentials\Exceptions;

class CredentialsValidator implements ICredentialsValidator
{
    /** @var Repositories\Credentials\IAuthTokenRepo The credentials repository */
    private $credentialsRepo = null;

    /**
     * @param Repositories\Credentials\IAuthTokenRepo $credentialsRepo The credentials repository
     */
    public function __construct(Repositories\Credentials\Repo $credentialsRepo)
    {
        $this->credentialsRepo = $credentialsRepo;
    }

    /**
     * Gets whether or not the input credentials are valid
     *
     * @param Credentials $credentials The credentials to validate
     * @return bool True if the credentials are valid, otherwise false
     * @throws Exceptions\TamperedCredentialsException Thrown if we suspect the credentials of having been tampered with
     */
    public function credentialsAreValid(Credentials $credentials)
    {

    }
} 