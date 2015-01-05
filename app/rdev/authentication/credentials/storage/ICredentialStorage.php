<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for various methods of credential storage to implement
 */
namespace RDev\Authentication\Credentials\Storage;
use RDev\Authentication\Credentials;
use RDev\HTTP\Responses;

interface ICredentialStorage
{
    /**
     * Deletes the credential from storage
     *
     * @param Responses\Response $response The response to delete the credential from
     */
    public function delete(Responses\Response $response);

    /**
     * Gets whether or not the credential is set in storage
     *
     * @return bool True if the credential exists in storage, otherwise false
     * @throws Credentials\InvalidCredentialException Thrown if the credentials exist but are invalid
     */
    public function exists();

    /**
     * Gets the credential from storage
     *
     * @return Credentials\ICredential|null The credential from storage if they exist, otherwise null
     * @throws Credentials\InvalidCredentialException Thrown if the credentials in storage are invalid
     */
    public function get();

    /**
     * Saves the credential to storage
     *
     * @param Responses\Response $response The response to save the credential to
     * @param Credentials\ICredential $credential The credential to save
     * @param string $unhashedToken The unhashed token to save
     */
    public function save(Responses\Response $response, Credentials\ICredential $credential, $unhashedToken);
} 