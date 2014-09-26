<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for various methods of credential storage to implement
 */
namespace RDev\Models\Authentication\Credentials\Storage;
use RDev\Models\Authentication\Credentials;
use RDev\Models\Authentication\Credentials\Exceptions;

interface ICredentialStorage
{
    /**
     * Deletes the credential from storage
     */
    public function delete();

    /**
     * Gets whether or not the credential is set in storage
     *
     * @return bool True if the credential exists in storage, otherwise false
     */
    public function exists();

    /**
     * Gets the credential from storage
     *
     * @return Credentials\ICredential|null The credential from storage if they exist, otherwise null
     * @throws Exceptions\InvalidCredentialException Thrown if the credentials in storage are invalid
     */
    public function get();

    /**
     * Saves the credential to storage
     *
     * @param Credentials\ICredential $credential The credential to save
     * @param string $unhashedToken The unhashed token to save
     */
    public function save(Credentials\ICredential $credential, $unhashedToken);
} 